<?php


namespace App\Customize\api\admin\handler;


use App\Customize\api\admin\model\CategoryModel;
use App\Customize\api\admin\model\ImageModel;
use App\Customize\api\admin\model\ImageProjectModel;
use App\Customize\api\admin\model\ModuleModel;
use App\Customize\api\admin\model\RelationTagModel;
use App\Customize\api\admin\model\ImageSubjectModel;
use App\Customize\api\admin\model\UserModel;
use App\Customize\api\admin\repository\FileRepository;
use App\Customize\api\admin\model\Model;
use stdClass;
use function api\admin\get_config_key_mapping_value;

use function core\convert_object;

class ImageProjectHandler extends Handler
{
    public static function handle($model): ?stdClass
    {
        if (empty($model)) {
            return null;
        }
        $model = convert_object($model);

        $model->__type__    = get_config_key_mapping_value('business.type_for_image_project' , $model->type);
        $model->__status__  = get_config_key_mapping_value('business.status_for_image_project' , $model->status);
        $model->__process_status__  = get_config_key_mapping_value('business.image_project_process_status' , $model->process_status);

        return $model;
    }

    // 附加：模块
    public static function module($model): void
    {
        if (empty($model)) {
            return ;
        }
        $module = property_exists($model , 'module') ? $model->module : ModuleModel::find($model->module_id);
        $module = ModuleHandler::handle($module);

        $model->module = $module;
    }

    // 附加：用户
    public static function user($model): void
    {
        if (empty($model)) {
            return ;
        }
        $user = property_exists($model , 'user') ? $model->user : UserModel::find($model->user_id);
        $user = UserHandler::handle($user);

        $model->user = $user;
    }


    // 附加：分类
    public static function category($model): void
    {
        if (empty($model)) {
            return ;
        }
        $category = property_exists($model , 'category') ? $model->category : CategoryModel::find($model->category_id);
        $category = CategoryHandler::handle($category);
        $model->category = $category;
    }


    // 附加：图片专题
    public static function imageSubject($model): void
    {
        if (empty($model)) {
            return ;
        }
        if ($model->type === 'pro') {
            $image_subject = property_exists($model , 'image_subject') ? $model->image_subject : ImageSubjectModel::find($model->image_subject_id);
            $image_subject = ImageSubjectHandler::handle($image_subject);
        } else {
            $image_subject = null;
        }
        $model->image_subject = $image_subject;
    }

    // 附加：图片专题
    public static function images($model): void
    {
        if (empty($model)) {
            return ;
        }
        $images = property_exists($model , 'images') ? $model->images : ImageModel::getByImageProjectId($model->id);
        $images = ImageHandler::handleAll($images);

        $model->images = $images;
    }

    public static function tags($model): void
    {
        if (empty($model)) {
            return ;
        }
        $tags = RelationTagModel::getByRelationTypeAndRelationId($model->type === 'pro' ? 'image_project' : 'image' , $model->id);
        $model->tags = $tags;
    }

    public static function imageCount($model): void
    {
        if (empty($model)) {
            return ;
        }
        $images = property_exists($model , 'images') ? $model->images : ImageModel::getByImageProjectId($model->id);
        $images = ImageHandler::handleAll($images);

        $model->image_count = count($images);
    }
}
