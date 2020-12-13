<?php


namespace App\Customize\api\admin\action;

use App\Customize\api\admin\handler\ImageProjectHandler;
use App\Customize\api\admin\job\ImageProjectResourceHandleJob;
use App\Customize\api\admin\model\CategoryModel;
use App\Customize\api\admin\model\ImageModel;
use App\Customize\api\admin\model\ImageProjectCommentImageModel;
use App\Customize\api\admin\model\ImageProjectCommentModel;
use App\Customize\api\admin\model\ImageProjectModel;
use App\Customize\api\admin\model\ModuleModel;
use App\Customize\api\admin\model\RelationTagModel;
use App\Customize\api\admin\model\ImageSubjectModel;
use App\Customize\api\admin\model\TagModel;
use App\Customize\api\admin\model\UserModel;
use App\Customize\api\admin\util\FileUtil;
use App\Customize\api\admin\util\ImageProjectUtil;
use App\Customize\api\admin\util\ResourceUtil;
use App\Http\Controllers\api\admin\Base;
use Core\Lib\File;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function api\admin\get_form_error;
use function api\admin\my_config;
use function api\admin\my_config_keys;
use function api\admin\parse_order;
use function core\array_unit;
use function core\current_datetime;

class ImageProjectAction extends Action
{
    public static function index(Base $context , array $param = []): array
    {
        $type_range     = my_config_keys('business.type_for_image_project');
        $status_range   = my_config_keys('business.status_for_image_project');
        $validator = Validator::make($param , [
            'type'              => ['sometimes' , Rule::in($type_range)] ,
            'status'            => ['sometimes' , 'integer' , Rule::in($status_range)] ,
            'user_id'           => 'sometimes|integer' ,
            'module_id'         => 'sometimes|integer' ,
            'category_id'       => 'sometimes|integer' ,
            'image_subject_id'  => 'sometimes|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $order = $param['order'] === '' ? [] : parse_order($param['order'] , '|');
        $limit = $param['limit'] === '' ? my_config('app.limit') : $param['limit'];
        $res = ImageProjectModel::index($param , $order , $limit);
        $res = ImageProjectHandler::handlePaginator($res , [
            'module' ,
            'user' ,
            'category' ,
            'image_subject' ,
            'images' ,
            'tags' ,
        ]);
        return self::success('' , $res);
    }

    public static function update(Base $context , $id , array $param = []): array
    {
        $type_range     = my_config_keys('business.type_for_image_project');
        $status_range   = my_config_keys('business.status_for_image_project');
        $validator = Validator::make($param , [
            'user_id'       => 'required|integer' ,
            'module_id'     => 'required|integer' ,
            'category_id'   => 'required|integer' ,
            'image_subject_id'    => 'sometimes|integer' ,
            'type'          => ['required' , Rule::in($type_range)] ,
            'weight'        => 'sometimes|integer' ,
            'view_count'    => 'sometimes|integer' ,
            'praise_count'  => 'sometimes|integer' ,
            'status'        => ['required' , 'integer' , Rule::in($status_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $image_project = ImageProjectModel::find($id);
        if (empty($image_project)) {
            return self::error('记录不存在' , '' , 404);
        }
        if (!in_array($image_project->process_status , [-1 , 2])) {
            return self::error('当前状态禁止操作！仅在：处理失败 或 处理完成 状态允许修改' , '' , 403);
        }
        if ($param['type'] === 'pro') {
            if ($param['name'] === '') {
                return self::error('名称尚未提供' , ['name' => '名称尚未提供']);
            }
            if (ImageProjectModel::findByNameAndExcludeId($param['name'] , $image_project->id)) {
                return self::error('名称已经被使用' , ['name' => '名称已经被使用']);
            }
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , ['module_id' => '模块不存在']);
        }
        $category = CategoryModel::find($param['category_id']);
        if (empty($category)) {
            return self::error('分类不存在' , ['category_id' => '分类不存在']);
        }
        $user = UserModel::find($param['user_id']);
        if (empty($user)) {
            return self::error('用户不存在' , ['user_id' => '用户不存在']);
        }
        $image_subject = null;
        if ($param['type'] === 'pro') {
            $image_subject = ImageSubjectModel::find($param['image_subject_id']);
            if (empty($image_subject)) {
                return self::error('专题不存在' , ['image_subject_id' => '专题不存在']);
            }
        }
        if ($param['status'] !== '' && $param['status'] == -1 && $param['fail_reason'] === '') {
            return self::error('请提供失败原因' , ['fail_reason' => '请提供失败原因']);
        }
        $datetime               = current_datetime();
        $param['weight']        = $param['weight'] === '' ? 0 : $param['weight'];
        $images                 = $param['images'] === '' ? [] : json_decode($param['images'] , true);
        $tags                   = $param['tags'] === '' ? [] : json_decode($param['tags'] , true);
        $param['process_status'] = 0;
        $param['updated_at']    = $datetime;
        $param['created_at']    = $param['created_at'] === '' ? $image_project->created_at : date('Y-m-d H:i:s' , strtotime($param['created_at']));
        try {
            DB::beginTransaction();
            ImageProjectModel::updateById($image_project->id , array_unit($param , [
                'name' ,
                'user_id' ,
                'module_id' ,
                'category_id' ,
                'type' ,
                'image_subject_id' ,
                'thumb' ,
                'description' ,
                'weight' ,
                'view_count' ,
                'praise_count' ,
                'status' ,
                'fail_reason' ,
                'process_status' ,
                'updated_at' ,
                'created_at' ,
            ]));
            ResourceUtil::used($param['thumb']);
            if ($image_project->thumb !== $param['thumb']) {
                ResourceUtil::delete($image_project->thumb);
            }
            $my_tags = RelationTagModel::getByRelationTypeAndRelationId('image_project' , $image_project->id);
            foreach ($tags as $v)
            {
                foreach ($my_tags as $v1)
                {
                    if ($v1->tag_id === $v) {
                        DB::rollBack();
                        return self::error('' , [
                            'tags' => '存在重复标签: name: ' . $v1->name . '; id: ' . $v1->tag_id ,
                        ]);
                    }
                }
                $tag = TagModel::find($v);
                if (empty($tag)) {
                    DB::rollBack();
                    return self::error('存在不存在的标签' , '' , 404);
                }
                RelationTagModel::insertGetId([
                    'relation_type' => 'image_project' ,
                    'relation_id'   => $image_project->id ,
                    'tag_id'        => $tag->id ,
                    'name'          => $tag->name ,
                    'module_id'     => $tag->module_id ,
                    'updated_at'    => $datetime ,
                    'created_at'    => $datetime ,
                ]);
                // 针对该标签的计数要增加
                TagModel::updateById($tag->id , [
                    'count' => ++$tag->count
                ]);
            }
            foreach ($images as $v)
            {
                ImageModel::insertGetId([
                    'image_project_id'  => $image_project->id ,
                    'src'               => $v ,
                    'updated_at'        => $datetime ,
                    'created_at'        => $datetime ,
                ]);
                ResourceUtil::used($v);
            }
            DB::commit();
            // 图片迁移
            ImageProjectResourceHandleJob::dispatch($id);
            return self::success('操作成功');
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function store(Base $context , array $param = []): array
    {
        $type_range     = my_config_keys('business.type_for_image_project');
        $status_range   = my_config_keys('business.status_for_image_project');
        $validator = Validator::make($param , [
//            'name'      => 'required' ,
            'user_id'       => 'required|integer' ,
            'module_id'     => 'required|integer' ,
            'category_id'   => 'required|integer' ,
            'image_subject_id'    => 'sometimes|integer' ,
            'type'          => ['required' , Rule::in($type_range)] ,
            'weight'        => 'sometimes|integer' ,
            'view_count'    => 'sometimes|integer' ,
            'praise_count'  => 'sometimes|integer' ,
            'status'        => ['required' , 'integer' , Rule::in($status_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        if ($param['type'] === 'pro') {
            if ($param['name'] === '') {
                return self::error('名称尚未提供' , ['name' => '名称尚未提供']);
            }
            if (ImageProjectModel::findByName($param['name'])) {
                return self::error('名称已经被使用' , ['name' => '名称已经被使用']);
            }
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , ['module_id' => '模块不存在']);
        }
        $category = CategoryModel::find($param['category_id']);
        if (empty($category)) {
            return self::error('分类不存在' , ['category_id' => '分类不存在']);
        }
        $user = UserModel::find($param['user_id']);
        if (empty($user)) {
            return self::error('用户不存在' , ['user_id' => '用户不存在']);
        }
        $image_subject = null;
        if ($param['type'] === 'pro') {
            $image_subject = ImageSubjectModel::find($param['image_subject_id']);
            if (empty($image_subject)) {
                return self::error('专题不存在' , ['image_subject_id' => '专题不存在']);
            }
        }
        if ($param['status'] !== '' && $param['status'] == -1 && $param['fail_reason'] === '') {
            return self::error('请提供失败原因' , ['fail_reason' => '请提供失败原因']);
        }
        $datetime               = current_datetime();
        $param['weight']        = $param['weight'] === '' ? 0 : $param['weight'];
        $param['updated_at']    = $datetime;
        $param['created_at']    = $param['created_at'] === '' ? $datetime : date('Y-m-d H:i:s' , strtotime($param['created_at']));
        $images                 = $param['images'] === '' ? [] : json_decode($param['images'] , true);
        $tags                   = $param['tags'] === '' ? [] : json_decode($param['tags'] , true);
        $param['process_status'] = 0;
        try {
            DB::beginTransaction();
            $id = ImageProjectModel::insertGetId(array_unit($param , [
                'name' ,
                'user_id' ,
                'module_id' ,
                'category_id' ,
                'type' ,
                'image_subject_id' ,
                'thumb' ,
                'description' ,
                'weight' ,
                'view_count' ,
                'praise_count' ,
                'status' ,
                'fail_reason' ,
                'process_status' ,
                'updated_at' ,
                'created_at' ,
            ]));
            ResourceUtil::used($param['thumb']);
            foreach ($tags as $v)
            {
                $tag = TagModel::find($v);
                if (empty($tag)) {
                    DB::rollBack();
                    return self::error('存在不存在的标签' , '' , 404);
                }
                RelationTagModel::insertGetId([
                    'relation_type'     => 'image_project' ,
                    'relation_id'       => $id ,
                    'tag_id'            => $tag->id ,
                    'name'              => $tag->name ,
                    'module_id'         => $tag->module_id ,
                    'updated_at'        => $datetime ,
                    'created_at'        => $datetime ,
                ]);
                // 针对该标签的计数要增加
                TagModel::updateById($tag->id , [
                    'count' => ++$tag->count
                ]);
            }
            foreach ($images as $v)
            {
                ImageModel::insertGetId([
                    'image_project_id'  => $id ,
                    'src'               => $v ,
                    'updated_at'        => $datetime ,
                    'created_at'        => $datetime ,
                ]);
                ResourceUtil::used($v);
            }
            DB::commit();
            // 图片迁移
            ImageProjectResourceHandleJob::dispatch($id);
            return self::success('操作成功');
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function show(Base $context , $id , array $param = []): array
    {
        $res = ImageProjectModel::find($id);
        if (empty($res)) {
            return self::error('记录不存在' , '' , 404);
        }
        $res = ImageProjectHandler::handle($res , [
            'module' ,
            'user' ,
            'category' ,
            'image_subject' ,
            'images' ,
            'tags' ,
        ]);
        return self::success('' , $res);
    }

    public static function destroy(Base $context , $id , array $param = []): array
    {
        try {
            DB::beginTransaction();
            ImageProjectUtil::delete($id);
            DB::commit();
            return self::success('操作成功');
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function destroyAll(Base $context , array $ids , array $param = []): array
    {

        try {
            DB::beginTransaction();
            foreach ($ids as $id)
            {
                ImageProjectUtil::delete($id);
            }
            DB::commit();
            return self::success('操作成功');
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function destroyImages(Base $context , array $ids , array $param = []): array
    {
        try {
            DB::beginTransaction();
            foreach ($ids as $v)
            {
                $res = ImageModel::find($v);
                if (empty($res)) {
                    DB::rollBack();
                    return self::error('部分图片记录不存在' , '' , 404);
                }
                ImageModel::destroy($res->id);
                ResourceUtil::delete($res->src);
            }
            DB::commit();
            return self::success('操作成功');
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // 删除单个标签
    public static function destroyTag(Base $context , array $param = []): array
    {
        $validator = Validator::make($param , [
            'image_project_id' => 'required|integer' ,
            'tag_id'           => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $count = RelationTagModel::delByRelationTypeAndRelationIdAndTagId('image_project' , $param['image_project_id'] , $param['tag_id']);
        return self::success('操作成功' , $count);
    }
}
