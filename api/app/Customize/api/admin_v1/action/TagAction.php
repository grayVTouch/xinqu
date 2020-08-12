<?php


namespace App\Customize\api\admin_v1\action;

use App\Customize\api\admin_v1\handler\TagHandler;
use App\Customize\api\admin_v1\model\ModuleModel;
use App\Customize\api\admin_v1\model\TagModel;
use App\Http\Controllers\api\admin_v1\Base;
use Illuminate\Support\Facades\Validator;
use function api\admin_v1\get_form_error;
use function api\admin_v1\my_config;
use function api\admin_v1\parse_order;
use function core\array_unit;
use function core\current_time;

class TagAction extends Action
{
    public static function index(Base $context , array $param = [])
    {
        $order = $param['order'] === '' ? [] : parse_order($param['order'] , '|');
        $limit = $param['limit'] === '' ? my_config('app.limit') : $param['limit'];
        $paginator = TagModel::index($param , $order , $limit);
        $paginator = TagHandler::handlePaginator($paginator);
        return self::success('' , $paginator);
    }

    public static function update(Base $context , $id , array $param = [])
    {
        $validator = Validator::make($param , [
            'name' => 'required' ,
            'module_id' => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $res = TagModel::find($id);
        if (empty($res)) {
            return self::error('标签不存在' , '' , 404);
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }
        $tag = TagModel::findByModuleIdAndNameExcludeSelf($res->id , $module->id , $param['name']);
        if (!empty($tag)) {
            return self::error('标签已经存在');
        }
        $param['weight'] = $param['weight'] === '' ? 0 : $param['weight'];
        TagModel::updateById($res->id , array_unit($param , [
            'name' ,
            'weight' ,
            'module_id' ,
        ]));
        return self::success();
    }

    public static function store(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'name' => 'required' ,
            'module_id' => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error('表单错误，请检查' , get_form_error($validator));
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }
        $tag = TagModel::findByModuleIdAndName($module->id , $param['name']);
        if (!empty($tag)) {
            return self::error('标签已经存在');
        }
        $param['weight'] = $param['weight'] === '' ? 0 : $param['weight'];
        $id = TagModel::insertGetId(array_unit($param , [
            'name' ,
            'weight' ,
            'module_id' ,
        ]));
        return self::success('' , $id);
    }

    public static function findOrCreate(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'name' => 'required' ,
            'module_id' => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error('表单错误，请检查' , get_form_error($validator));
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在' , '' , 404);
        }
        $tag = TagModel::findByModuleIdAndName($module->id , $param['name']);
        if (!empty($tag)) {
            return self::success('' , $tag);
        }
        $param['weight'] = $param['weight'] === '' ? 0 : $param['weight'];
        $param['create_time'] = current_time();
        $id = TagModel::insertGetId(array_unit($param , [
            'name' ,
            'weight' ,
            'module_id' ,
            'create_time' ,
        ]));
        $tag = TagModel::find($id);
        TagHandler::handle($tag);
        return self::success('' , $tag);
    }


    public static function show(Base $context , $id , array $param = [])
    {
        $res = TagModel::find($id);
        if (empty($role)) {
            return self::error('标签不存在' , '' , 404);
        }
        $res = TagHandler::handle($res);
        return self::success('' , $res);
    }

    public static function destroy(Base $context , $id , array $param = [])
    {
        $count = TagModel::destroy($id);
        return self::success('' , $count);
    }

    public static function destroyAll(Base $context , array $ids , array $param = [])
    {
        $count = TagModel::destroy($ids);
        return self::success('' , $count);
    }

    public static function search(Base $context , $value , array $param = [])
    {
        if (empty($value)) {
            return self::error('请提供搜索值');
        }
        $res = TagModel::search($value);
        $res = TagHandler::handleAll($res);
        return self::success('' , $res);
    }

    public static function topByModuleId(Base $context , int $module_id ,  array $param = [])
    {
        if (empty($module_id)) {
            return self::success('' , []);
        }
        $limit = 10;
        $res = TagModel::topByModuleId($module_id , $limit);
        $res = TagHandler::handleAll($res);
        return self::success('' , $res);
    }
}
