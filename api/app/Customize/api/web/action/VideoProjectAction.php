<?php


namespace App\Customize\api\web\action;

use App\Customize\api\web\handler\CollectionGroupHandler;
use App\Customize\api\web\handler\VideoHandler;
use App\Customize\api\web\handler\VideoProjectHandler;
use App\Customize\api\web\handler\RelationTagHandler;
use App\Customize\api\web\handler\ImageSubjectHandler;
use App\Customize\api\web\model\CategoryModel;
use App\Customize\api\web\model\CollectionGroupModel;
use App\Customize\api\web\model\CollectionModel;
use App\Customize\api\web\model\HistoryModel;
use App\Customize\api\web\model\VideoModel;
use App\Customize\api\web\model\VideoSeriesModel;
use App\Customize\api\web\model\VideoProjectModel;
use App\Customize\api\web\model\ModuleModel;
use App\Customize\api\web\model\PraiseModel;
use App\Customize\api\web\model\RelationTagModel;
use App\Customize\api\web\model\ImageSubjectModel;
use App\Customize\api\web\model\TagModel;
use App\Http\Controllers\api\web\Base;
use Core\Lib\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function api\web\get_form_error;
use function api\web\my_config;
use function api\web\my_config_keys;
use function api\web\parse_order;
use function api\web\user;
use function core\current_datetime;
use function core\object_to_array;

class VideoProjectAction extends Action
{
    public static function newest(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }

        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = VideoProjectModel::getNewestByFilterAndLimit($param , $limit);
        $res = VideoProjectHandler::handleAll($res);
        foreach ($res as $v)
        {
            VideoProjectHandler::isCollected($v);
            VideoProjectHandler::isPraised($v);
        }
        return self::success('' , $res);
    }

    public static function hot(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }

        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = VideoProjectModel::getHotByFilterAndLimit($param , $limit);
        $res = VideoProjectHandler::handleAll($res);
        foreach ($res as $v)
        {
            VideoProjectHandler::isCollected($v);
            VideoProjectHandler::isPraised($v);
        }
        return self::success('' , $res);
    }

    public static function hotWithPager(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }

        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = VideoProjectModel::getHotWithPagerByFilterAndLimit($param , $limit);
        $res = VideoProjectHandler::handlePaginator($res);
        return self::success('' , $res);
    }

    public static function getByTagId(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'tag_id' => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        $tag = TagModel::find($param['tag_id']);
        if (empty($tag)) {
            return self::error('标签不存在');
        }

        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = VideoProjectModel::getByTagIdAndFilterAndLimit($tag->id , $param , $limit);
        $res = VideoProjectHandler::handleAll($res);
        foreach ($res as $v)
        {
            VideoProjectHandler::isCollected($v);
            VideoProjectHandler::isPraised($v);
        }
        return self::success('' , $res);
    }

    public static function getWithPagerByTagIds(Base $context , array $param = [])
    {
        $mode_range = my_config('business.mode_for_video_project');

        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'tag_ids'   => 'required' ,
            'mode'      => ['required' , Rule::in($mode_range)] ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }

        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }

        $tag_ids = empty($param['tag_ids']) ? [] : json_decode($param['tag_ids'] , true);
        if (empty($tag_ids)) {
            return self::error('请提供过滤的标签');
        }

        $tags = TagModel::find($tag_ids);
        if (count($tags) !== count($tag_ids)) {
            return self::error('部分或全部标签未找到');
        }

        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];

        switch ($param['mode'])
        {
            case 'strict':
                $res = VideoProjectModel::getInStrictByTagIdsAndFilterAndLimit($tag_ids , $param , $limit);
                break;
            case 'loose':
                $res = VideoProjectModel::getByTagIdsAndFilterAndLimit($tag_ids , $param , $limit);
                break;
            default:
                return self::error('不支持的 mode ，当前受支持的 mode 有：' . implode(' , ' , $mode_range));
        }

        $res = VideoProjectHandler::handlePaginator($res);
        return self::success('' , $res);
    }


    public static function hotTags(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }

        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }

        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = RelationTagModel::hotTagsInVideoSubjectByFilterAndLimit($param , $limit);
        $res = RelationTagHandler::handleAll($res);
        return self::success('' , $res);
    }

    public static function hotTagsWithPager(Base $context , array $param = [])
    {
        $type_range = my_config_keys('business.type_for_video_project');
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'type'      => ['required' , Rule::in($type_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = RelationTagModel::hotTagsWithPagerInVideoProjectByValueAndFilterAndLimit($param['value'] , $param , $limit);
        $res = RelationTagHandler::handlePaginator($res);
        return self::success('' , $res);
    }

    public static function newestWithPager(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }

        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }

        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = VideoProjectModel::getNewestWithPagerByFilterAndLimit($param , $limit);
        $res = VideoProjectHandler::handlePaginator($res);
        return self::success('' , $res);
    }

    public static function show(Base $context , $id , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }
        $video_project = VideoProjectModel::find($id);
        if (empty($video_project)) {
            return self::error('图片专题不存在' , '' , 404);
        }
        $video_project = VideoProjectHandler::handle($video_project);
        // 附加：视频专题播放记录
        VideoProjectHandler::userPlayRecord($video_project);
        VideoProjectHandler::isPraised($video_project);
        VideoProjectHandler::isCollected($video_project);

        return self::success('' , $video_project);
    }

    public static function category(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }
        $categories = CategoryModel::getByModuleIdAndType($module->id , 'video_project');
        $categories = object_to_array($categories);
        $categories = Category::childrens(0 , $categories , null , false , false);
        return self::success('' , $categories);
    }

    public static function subject(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }
        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = ImageSubjectModel::getWithPagerInImageProjectByModuleIdAndValueAndLimit($module->id , $param['value'] , $limit);
        $res = ImageSubjectHandler::handlePaginator($res);
        return self::success('' , $res);
    }

    public static function index(Base $context , array $param = [])
    {
        $type_range = my_config_keys('business.type_for_video_project');
        $mode_range = my_config('business.mode_for_video_project');

        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'mode'      => ['required' , Rule::in($mode_range)] ,
            'type'      => ['required' , Rule::in($type_range)] ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }

        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }

        $param['category_ids']   = $param['category_ids'] === '' ? [] : json_decode($param['category_ids'] , true);
        $param['video_series_ids']    = $param['video_series_ids'] === '' ? [] : json_decode($param['video_series_ids'] , true);
        $param['video_company_ids']    = $param['video_company_ids'] === '' ? [] : json_decode($param['video_company_ids'] , true);
        $param['tag_ids']        = $param['tag_ids'] === '' ? [] : json_decode($param['tag_ids'] , true);

        $order                   = $param['order'] === '' ? null : parse_order($param['order']);
        $limit                   = empty($param['limit']) ? my_config('app.limit') : $param['limit'];

        // 获取所有子类
        $categories         = CategoryModel::getAll();
        $categories         = object_to_array($categories);
        $tmp_category_ids   = [];

        foreach ($param['category_ids'] as $v)
        {
            $childrens          = Category::childrens($v , $categories , null , true , false);
            $ids                = array_column($childrens , 'id');
            $tmp_category_ids   = array_merge($tmp_category_ids , $ids);
        }

        $param['category_ids'] = array_unique($tmp_category_ids);
        $res = [];
        switch ($param['mode'])
        {
            case 'strict':
                $res = VideoProjectModel::getWithPagerInStrictByFilterAndOrderAndLimit($param , $order , $limit);
                break;
            case 'loose':
                $res = VideoProjectModel::getWithPagerInLooseByFilterAndOrderAndLimit($param , $order , $limit);
                break;
            default:
                return self::error('不支持的搜索模式，当前支持的模式有：' . implode(' , ' , $mode_range));
        }
        $res = VideoProjectHandler::handlePaginator($res);
        foreach ($res->data as $v)
        {
            VideoProjectHandler::isCollected($v);
            VideoProjectHandler::isPraised($v);
        }
        return self::success('' , $res);
    }

    public static function incrementViewCount(Base $context , int $video_project_id , array $param = [])
    {
        $video_project = VideoProjectModel::find($video_project_id);
        if (empty($video_project)) {
            return self::error('图片专题不存在');
        }
        VideoProjectModel::countHandle($video_project->id , 'view_count' , 'increment' , 1);
        return self::success('操作成功');
    }

    // 推荐数据
    public static function recommend(Base $context , int $video_project_id , array $param = [])
    {
        $type_range = my_config_keys('business.type_for_video_project');

        $validator = Validator::make($param , [
            'type'      => ['required' , Rule::in($type_range)] ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }

        $video_project = VideoProjectModel::find($video_project_id);
        if (empty($video_project)) {
            return self::error('图片专题未找到' , null , 404);
        }

        $param['module_id']     = $video_project->module_id ?? '';
        $param['category_id']   = $video_project->category_id ?? '';
        $param['image_subject_id']    = $video_project->image_subject_id ?? '';

        $limit = $param['limit'] ? $param['limit'] : my_config('app.limit');

        $res = VideoProjectModel::recommendExcludeSelfByFilterAndLimit($video_project->id , $param , $limit);
        $res = VideoProjectHandler::handleAll($res);
        return self::success('' , $res);
    }

    public static function videosInRange(Base $context , int $video_project_id , array $param = [])
    {
        $validator = Validator::make($param , [
            'min' => 'required|integer' ,
            'max' => 'required|integer' ,
        ]);

        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }

        $video_project = VideoProjectModel::find($video_project_id);
        if (empty($video_project)) {
            return self::error('记录不存在' , null , 404);
        }

        $res = VideoModel::getByVideoProjectIdAndMinIndexAndMaxIndex($video_project->id , $param['min'] , $param['max']);
        $res = VideoHandler::handleAll($res);
        return self::success('' , $res);
    }

    public static function getByVideoSeriesId(Base $context , array $param = []): array
    {
        $validator = Validator::make($param , [
            'video_project_id' => 'required|integer' ,
            'video_series_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        $video_series = VideoSeriesModel::find($param['video_series_id']);
        if (empty($video_series)) {
            return self::error('视频系列不存在' , '' , 404);
        }
        $res = VideoProjectModel::getByVideoSeriesIdAndExcludeVideoProjectId($video_series->id , $param['video_project_id']);
        $res = VideoProjectHandler::handleAll($res);
        return self::success('' , $res);
    }

    public static function praiseHandle(Base $context , int $id , array $param = []): array
    {
        $action_range = my_config_keys('business.bool_for_int');
        $validator = Validator::make($param , [
            'module_id'     => 'required|integer' ,
            'action'        => ['required' , Rule::in($action_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $video_project = VideoProjectModel::find($id);
        if (empty($video_project)) {
            return self::error('视频不存在' , '' , 404);
        }
        $relation_type = 'video_project';
        $relation_id = $video_project->id;
        $datetime = date('Y-m-d H:i:s');
        $user = user();
        try {
            DB::beginTransaction();
            // 视频专题
            if ($param['action'] == 1) {
                $praise = PraiseModel::findByModuleIdAndUserIdAndRelationTypeAndRelationId($module->id , $user->id , $relation_type , $relation_id);
                if (empty($praise)) {
                    PraiseModel::insertOrIgnore([
                        'module_id' => $module->id ,
                        'user_id' => $user->id ,
                        'relation_type' => $relation_type ,
                        'relation_id' => $relation_id ,
                        'created_at' => $datetime
                    ]);
                }
                VideoProjectModel::incrementByIdAndColumnAndStep($video_project->id , 'praise_count' , 1);
            } else {
                PraiseModel::delByModuleIdAndUserIdAndRelationTypeAndRelationId($module->id , $user->id , $relation_type , $relation_id);
                VideoProjectModel::decrementByIdAndColumnAndStep($video_project->id , 'praise_count' , 1);
            }
            DB::commit();
            return self::success('操作成功');
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
