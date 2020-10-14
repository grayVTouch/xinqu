<?php


namespace App\Customize\api\admin\handler;


use App\Customize\api\admin\model\ModuleModel;
use App\Customize\api\admin\model\TagModel;
use App\Customize\api\admin\model\Model;
use App\Customize\api\admin\model\UserModel;
use stdClass;
use function api\admin\get_config_key_mapping_value;
use function core\convert_object;

class TagHandler extends Handler
{
    public static function handle(?Model $model , array $with = []): ?stdClass
    {
        if (empty($model)) {
            return null;
        }
        $model = convert_object($model);

        $model->__status__ = get_config_key_mapping_value('business.status_for_tag' , $model->status);

        if (in_array('user' , $with)) {
            $user = UserModel::find($model->user_id);
            $user = UserHandler::handle($user);
            $model->user = $user;
        }
        if (in_array('module' , $with)) {
            $module = ModuleModel::find($model->module_id);
            $module = ModuleHandler::handle($module);
            $model->module = $module;
        }

        return $model;
    }

}