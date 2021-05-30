<?php


namespace App\Customize\api\admin\handler;


use App\Customize\api\admin\model\CategoryModel;
use App\Customize\api\admin\model\ModuleModel;
use App\Customize\api\admin\model\RelationTagModel;
use App\Customize\api\admin\model\UserModel;
use App\Customize\api\admin\model\VideoCompanyModel;
use App\Customize\api\admin\model\VideoSeriesModel;
use App\Customize\api\admin\model\Model;
use stdClass;

use function api\admin\get_config_key_mapping_value;
use function core\convert_object;

class VideoProjectHandler extends Handler
{
    public static function handle(?Model $model): ?stdClass
    {
        if (empty($model)) {
            return null;
        }
        $model = convert_object($model);

        $model->__end_status__  = empty($model->end_status) ? '' : get_config_key_mapping_value('business.end_status_for_video_project' , $model->status);
        $model->__status__      = empty($model->status) ? '' : get_config_key_mapping_value('business.status_for_video_project' , $model->status);

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

    public static function videoSeries($model): void
    {
        if (empty($model)) {
            return ;
        }
        $video_series = VideoSeriesModel::find($model->video_series_id);
        $video_series = VideoSeriesHandler::handle($video_series);
        $model->video_series = $video_series;
    }


    public static function videoCompany($model): void
    {
        if (empty($model)) {
            return ;
        }
        $video_company = VideoCompanyModel::find($model->video_company_id);
        $video_company = VideoCompanyHandler::handle($video_company);
        $model->video_company = $video_company;
    }


    public static function category($model): void
    {
        if (empty($model)) {
            return ;
        }
        $category = CategoryModel::find($model->category_id);
        $category = CategoryHandler::handle($category);
        $model->category = $category;
    }

    public static function tags($model): void
    {
        if (empty($model)) {
            return ;
        }
        $tags = RelationTagModel::getByRelationTypeAndRelationId('video_project' , $model->id);
        $model->tags = $tags;
    }
}
