<?php


use App\Customize\api\web\middleware\CustomizeMiddleware;
use App\Customize\api\web\middleware\UserAuthMiddleware;
use App\Customize\api\web\middleware\UserMiddleware;
use App\Http\Controllers\api\web\Category;
use App\Http\Controllers\api\web\File;
use App\Http\Controllers\api\web\ImageAtPosition;
use App\Http\Controllers\api\web\ImageProject;
use App\Http\Controllers\api\web\Misc;
use App\Http\Controllers\api\web\Module;
use App\Http\Controllers\api\web\Nav;
use App\Http\Controllers\api\web\ImageSubject;
use App\Http\Controllers\api\web\Tag;
use App\Http\Controllers\api\web\User;
use App\Http\Controllers\api\web\VideoCompany;
use App\Http\Controllers\api\web\VideoProject;
use App\Http\Controllers\api\web\VideoSeries;
use Illuminate\Support\Facades\Route;

Route::prefix('web')
    ->middleware([
        CustomizeMiddleware::class ,
    ])
    ->name('api.web.')
    ->group(function(){
        Route::middleware([
            UserMiddleware::class
        ])->group(function(){
            // 不用登录的相关接口

            // 文件上传
            Route::post('upload'        , [File::class      , 'upload']);
            // 上传图片
            Route::post('upload_image'  , [File::class      , 'uploadImage']);
            // 上传视频
            Route::post('upload_video'  , [File::class      , 'uploadVideo']);
            // 上传字幕
            Route::post('upload_subtitle' , [File::class    , 'uploadSubtitle']);
            // 上传办公文件
            Route::post('upload_office' , [File::class      , 'uploadOffice']);

            /***
             * *****************
             * 模块相关接口
             * *****************
             */

            // 所有模块
            Route::get('module'                     , [Module::class        , 'all']);
            // 默认模块
            Route::get('default_module'             , [Module::class        , 'default']);
            // 所有分类
            Route::get('category'                   , [Category::class      , 'all']);
            // 所有导航菜单
            Route::get('nav'                        , [Nav::class           , 'all']);
            // 首页幻灯片
            Route::get('home_slideshow'             , [ImageAtPosition::class , 'home']);
            // 图片专题幻灯片
            Route::get('image_project_slideshow'    , [ImageAtPosition::class , 'imageProject']);


            /**
             * ****************
             * 图片专题
             * ****************
             */

            // 图片专题 - 最新
            Route::get('image_project/newest'       , [ImageProject::class , 'newest']);
            // 图片专题 - 热门
            Route::get('image_project/hot'          , [ImageProject::class , 'hot']);
            // 图片专题 - 最新 - 分页
            Route::get('image_project/newest_with_pager'        , [ImageProject::class , 'newestWithPager']);
            // 图片专题 - 热门 - 分页
            Route::get('image_project/hot_with_pager'           , [ImageProject::class , 'hotWithPager']);
            // 图片专题 - 通过标签过滤
            Route::get('image_project/get_by_tag_id'   , [ImageProject::class , 'getByTagId']);
            // 图片专题 - 通过标签过滤 - 分页
            Route::get('image_project/get_with_pager_by_tag_ids' , [ImageProject::class , 'getWithPagerByTagIds']);
            // 图片专题 - 热门标签
            Route::get('image_project/hot_tags'             , [ImageProject::class , 'hotTags']);
            // 图片专题 - 热门标签 - 分页
            Route::get('image_project/hot_tags_with_pager'  , [ImageProject::class , 'hotTagsWithPager']);
            // 图片专题 - 分类
            Route::get('image_project/category'             , [ImageProject::class , 'category']);
            // 图片专题 - 推荐列表
            Route::get('image_project/{id}/recommend' , [ImageProject::class , 'recommend']);
            // 图片专题 - 图片主体
            Route::get('image_project/image_subject'  , [ImageProject::class , 'imageSubject']);
            // 团偏专题 - 详情
            Route::get('image_project/{id}'     , [ImageProject::class , 'show']);
            // 图片专题 - 列表
            Route::get('image_project'          , [ImageProject::class , 'index']);

            // 图片主体 - 详情
            Route::get('image_subject/{id}' , [ImageSubject::class , 'show']);
            // 图片专题 - 增加浏览量
            Route::post('image_project/{id}/increment_view_count' , [ImageProject::class , 'incrementViewCount']);

            // 分类 - 详情
//            Route::get('category/{id}' , [Category::class , 'show']);
//            Route::get('tag/{id}' , [Tag::class , 'show']);

            // 验证码
            Route::get('captcha' , [Misc::class , 'captcha']);
            // 用户 - 登录
            Route::post('login' , [User::class , 'login']);
            // 用户 - 注册
            Route::post('register' , [User::class , 'store']);
            // 用户 - 更新密码
            Route::patch('user/update_password' , [User::class , 'updatePassword']);
            // 用户 - 发送更新密码的验证码
            Route::post('send_email_code_for_password' , [Misc::class , 'sendEmailCodeForPassword']);
            // 用户 - 发送注册的验证码
            Route::post('send_email_code_for_register' , [Misc::class , 'sendEmailCodeForRegister']);
            // 用户 - 我关注的用户
            Route::get('user/{id}/my_focus_user' , [User::class , 'myFocusUser']);
            // 用户 - 关注我的用户
            Route::get('user/{id}/focus_me_user' , [User::class , 'focusMeUser']);
            // 用户 - 我的收藏夹
            Route::get('user/{id}/collection_group' , [User::class , 'collectionGroupByUserId']);
            // 用户 - 详情
            Route::get('user/{id}/show' , [User::class , 'show']);
            // 用户 - 收藏夹信息
            Route::get('user/{collection_group_id}/collection_group_info' , [User::class , 'collectionGroupInfo']);
            // 用户 - 我的收藏
            Route::get('user/collections' , [User::class , 'collections']);

            /**
             * ************************************
             * 视频专题
             * ************************************
             */

            // 视频专题 - 列表
            Route::get('video_project' , [VideoProject::class , 'index']);
            // 视频专题 - 最新
            Route::get('video_project/newest' , [VideoProject::class , 'newest']);
            // 视频专题 - 热门标签
            Route::get('video_project/hot_tags' , [VideoProject::class , 'hotTags']);
            // 视频专题 - 热门
            Route::get('video_project/hot' , [VideoProject::class , 'hot']);
            // 视频专题 - 通过标签过滤
            Route::get('video_project/get_by_tag_id' , [VideoProject::class , 'getByTagId']);
            // 视频专题 - 通过标签过滤
            Route::get('video_project/get_by_tag_ids' , [VideoProject::class , 'getByTagIds']);
            // 视频专题 - 热门标签 - 分页
            Route::get('video_project/hot_tags_with_pager'  , [VideoProject::class , 'hotTagsWithPager']);
            // 视频专题-分类
            Route::get('video_project/category'             , [VideoProject::class , 'category']);
            // 视频专题 - 详情
            Route::get('video_project/{id}'        , [VideoProject::class , 'show']);
            // 视频专题 - 视频范围
            Route::get('video_project/{id}/videos_in_range' , [VideoProject::class , 'videosInRange']);
            // 视频专题 - 给定系列下的
            Route::get('video_project_filter_by_video_series_id' , [VideoProject::class , 'getByVideoSeriesId']);
            // 视频专题 - 视频系列
            Route::get('video_series' , [VideoSeries::class , 'index']);
            // 视频专题 - 视频公司
            Route::get('video_company' , [VideoCompany::class , 'index']);

        });

        Route::middleware([
            UserAuthMiddleware::class
        ])->group(function(){
            // 要求登录的相关接口
            Route::post('user/collection_handle'    , [User::class , 'collectionHandle']);
            Route::post('user/praise_handle'        , [User::class , 'praiseHandle']);
            Route::post('user/record'               , [User::class , 'record']);
            Route::post('user/create_and_join_collection_group' , [User::class , 'createAndJoinCollectionGroup']);
            Route::post('user/create_collection_group' , [User::class , 'createCollectionGroup']);
            Route::post('user/join_collection_group' , [User::class , 'joinCollectionGroup']);
            Route::delete('user/destroy_collection_group' , [User::class , 'destroyCollectionGroup']);
            Route::delete('user/destroy_all_collection_group' , [User::class , 'destroyAllCollectionGroup']);
            Route::delete('user/destroy_collection' , [User::class , 'destroyCollection']);
            Route::delete('user/destroy_history' , [User::class , 'destroyHistory']);
            Route::get('user/collection_group_with_judge' , [User::class , 'collectionGroupWithJudge']);

            Route::get('user/collection_group' , [User::class , 'collectionGroup']);

            Route::get('user_info' , [User::class , 'info']);
            Route::get('less_history' , [User::class , 'lessHistory']);
            Route::get('history' , [User::class , 'histories']);
            Route::get('less_relation_in_collection' , [User::class , 'lessRelationInCollection']);
            Route::get('less_collection_group_with_collection' , [User::class , 'lessCollectionGroupWithCollection']);
            Route::put('update_user' , [User::class , 'update']);
            Route::patch('update_user' , [User::class , 'localUpdate']);
            Route::patch('user/update_password_in_logged' , [User::class , 'updatePasswordInLogged']);
            Route::patch('user/update_collection_group' , [User::class , 'updateCollectionGroup']);

            // 关注用户
            Route::post('user/focus_handle' , [User::class , 'focusHandle']);


        });
    });
