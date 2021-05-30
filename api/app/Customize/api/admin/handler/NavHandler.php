<?php


namespace App\Customize\api\admin\handler;


use App\Customize\api\admin\model\Model;
use App\Customize\api\admin\model\ModuleModel;
use App\Customize\api\admin\model\NavModel;
use stdClass;
use function api\admin\get_config_key_mapping_value;

use function core\convert_object;

class NavHandler extends Handler
{
    public static function handle(?Model $model): ?stdClass
    {
        if (empty($model)) {
            return null;
        }

        $model = convert_object($model);

        $model->__is_enabled__ = get_config_key_mapping_value('business.bool_for_int' , $model->is_enabled);
        $model->__type__ = get_config_key_mapping_value('business.type_for_nav' , $model->type);

        return $model;
    }

    public static function module($model): void
    {
        if (empty($model)) {
            return ;
        }
        $module = ModuleModel::find($model->module_id);
        $module = ModuleHandler::handle($module);
        $model->module = $module;
    }

    public static function parent($model , bool $deep = false): void
    {
        if (empty($model)) {
            return ;
        }
        $nav = $model->p_id ? NavModel::find($model->p_id) : null;
        if ($deep) {
            self::parent($nav , $deep);
        }
        $model->parent = $nav;
    }


}
