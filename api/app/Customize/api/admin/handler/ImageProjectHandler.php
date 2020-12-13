<?php


namespace App\Customize\api\admin\handler;


use App\Customize\api\admin\model\CategoryModel;
use App\Customize\api\admin\model\ImageModel;
use App\Customize\api\admin\model\ImageProjectModel;
use App\Customize\api\admin\model\ModuleModel;
use App\Customize\api\admin\model\RelationTagModel;
use App\Customize\api\admin\model\ImageSubjectModel;
use App\Customize\api\admin\model\UserModel;
use App\Customize\api\admin\util\FileUtil;
use App\Customize\api\admin\model\Model;
use stdClass;
use function api\admin\get_config_key_mapping_value;

use function core\convert_object;

class ImageProjectHandler extends Handler
{
    public static function handle(?Model $model , array $with = []): ?stdClass
    {
        if (empty($model)) {
            return null;
        }
        $model = convert_object($model);

        $model->__type__    = get_config_key_mapping_value('business.type_for_image_project' , $model->type);
        $model->__status__  = get_config_key_mapping_value('business.status_for_image_project' , $model->status);
        $model->__process_status__  = get_config_key_mapping_value('business.image_process_status' , $model->process_status);

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
        if (in_array('category' , $with)) {
            $category = CategoryModel::find($model->category_id);
            $category = CategoryHandler::handle($category);
            $model->category = $category;
        }
        if (in_array('image_subject' , $with)) {
            if ($model->type === 'pro') {
                $image_subject = ImageSubjectModel::find($model->image_subject_id);
                $image_subject = ImageSubjectHandler::handle($image_subject);
            } else {
                $image_subject = null;
            }
            $model->image_subject = $image_subject;
        }
        if (in_array('images' , $with)) {
            $images = ImageModel::getByImageProjectId($model->id);
            $images = ImageHandler::handleAll($images);
            $model->images = $images;
        }
        if (in_array('tags' , $with)) {
            $tags = RelationTagModel::getByRelationTypeAndRelationId('image_project' , $model->id);
            $model->tags = $tags;
        }

        return $model;
    }

}
