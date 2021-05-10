<?php


namespace App\Customize\api\admin\handler;


use App\Customize\api\admin\model\CategoryModel;
use App\Customize\api\admin\model\Model;
use App\Customize\api\admin\model\ModuleModel;
use App\Customize\api\admin\model\UserModel;
use stdClass;
use Traversable;
use function api\admin\get_config_key_mapping_value;
use function core\convert_object;

class CategoryHandler extends Handler
{
    public static function handle(?Model $model , array $with = [] , bool $deep = true): ?stdClass
    {
        if (empty($model)) {
            return null;
        }
        $model = convert_object($model);

        $model->__type__ = get_config_key_mapping_value('business.category_type' , $model->type);
        $model->__status__  = get_config_key_mapping_value('business.status_for_category' , $model->status);
        $model->__is_enabled__  = get_config_key_mapping_value('business.bool_for_int' , $model->is_enabled);

        return $model;
    }

    public static function user($model): void
    {
        if (empty($model)) {
            return ;
        }
        $user = UserModel::find($model->user_id);
        $user = UserHandler::handle($user);
        $model->user = $user;
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

    public static function parent($model , bool $deep = true): void
    {
        if (empty($model)) {
            return ;
        }
        if ($deep) {
            $category = $model->p_id ? CategoryModel::find($model->p_id) : null;
            self::parent($category , true);
        } else {
            $category = null;
        }
        $model->parent = $category;
    }


}
