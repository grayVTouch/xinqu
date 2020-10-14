<?php


use App\Customize\api\web\middleware\CustomizeMiddleware;
use App\Customize\api\web\middleware\UserAuthMiddleware;
use App\Customize\api\web\middleware\UserMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('web')
    ->namespace('api\\web')
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
            Route::post('upload' , 'File@upload');
            Route::post('upload_image' , 'File@uploadImage');
            Route::post('upload_video' , 'File@uploadVideo');
            Route::post('upload_subtitle' , 'File@uploadSubtitle');
            Route::post('upload_office' , 'File@uploadOffice');

            /***
             * *****************
             * 模块相关接口
             * *****************
             */
            Route::get('module' , 'Module@all');
            Route::get('default_module' , 'Module@default');
            Route::get('category' , 'Category@all');
            Route::get('nav' , 'Nav@all');
            Route::get('image_at_position/home_slideshow' , 'ImageAtPosition@homeSlideshow');
            Route::get('image_at_position/image_subject' , 'ImageAtPosition@imageSubject');


            /**
             * ****************
             * 图片专题
             * ****************
             */
            Route::get('image_subject/newest' , 'ImageSubject@newest');
            Route::get('image_subject/hot' , 'ImageSubject@hot');
            Route::get('image_subject/newest_with_pager' , 'ImageSubject@newestWithPager');
            Route::get('image_subject/hot_with_pager' , 'ImageSubject@hotWithPager');
            Route::get('image_subject/{tag_id}/get_by_tag_id' , 'ImageSubject@getByTagId');
            Route::get('image_subject/get_with_pager_by_tag_ids' , 'ImageSubject@getWithPagerByTagIds');
            Route::get('image_subject/hot_tags' , 'ImageSubject@hotTags');
            Route::get('image_subject/hot_tags_with_pager' , 'ImageSubject@hotTagsWithPager');
            Route::get('image_subject/category' , 'ImageSubject@category');
            Route::get('image_subject/{image_subject_id}/recommend' , 'ImageSubject@recommend');

            // 特别注意，下面这个路由仅允许放置到最后一个，否则，符合条件的路由都会被导向到这个路由里面去
            // 这种情况下，就会出现定义的具体路由不生效的情况
            Route::get('image_subject/subject' , 'ImageSubject@subject');
            Route::get('image_subject/{id}' , 'ImageSubject@show');
            Route::get('image_project' , 'ImageSubject@index');


            Route::get('subject/{id}' , 'Subject@show');
            Route::get('category/{id}' , 'Category@show');
            Route::get('tag/{id}' , 'Tag@show');
            Route::get('captcha' , 'Misc@captcha');

            Route::post('login' , 'User@login');
            Route::post('register' , 'User@store');
            Route::patch('user/update_password' , 'User@updatePassword');
            Route::patch('image_subject/{image_subject_id}/increment_view_count' , 'ImageSubject@incrementViewCount');

            Route::post('send_email_code_for_password' , 'Misc@sendEmailCodeForPassword');
            Route::post('send_email_code_for_register' , 'Misc@sendEmailCodeForRegister');

            Route::get('user/{user_id}/my_focus_user' , 'User@myFocusUser');
            Route::get('user/{user_id}/focus_me_user' , 'User@focusMeUser');
            Route::get('user/{user_id}/collection_group' , 'User@collectionGroupByUserId');
            Route::get('user/{user_id}/show' , 'User@show');
            Route::get('user/{collection_group_id}/collection_group_info' , 'User@collectionGroupInfo');
            Route::get('user/collections' , 'User@collections');

            /**
             * ************************************
             * 视频专题
             * ************************************
             */
            Route::get('/video_project/newest' , 'VideoSubject@newest');
            Route::get('/video_project/hot_tags' , 'VideoSubject@hotTags');
            Route::get('/video_project/hot' , 'VideoSubject@hot');
            Route::get('/video_project/{tag_id}/get_by_tag_id' , 'VideoSubject@getByTagId');
            Route::get('/video_project/get_by_tag_ids' , 'VideoSubject@getByTagIds');
            Route::get('/video_project/{id}' , 'VideoSubject@show');
            Route::get('/video_project/{id}/videos_in_range' , 'VideoSubject@videosInRange');

            /**
             * 视频系列
             */
            Route::get('/video_project/{id}/video_projects' , 'VideoSubject@videoSubjectsInSeries');

        });

        Route::middleware([
            UserAuthMiddleware::class
        ])->group(function(){
            // 要求登录的相关接口
            Route::post('user/collection_handle' , 'User@collectionHandle');
            Route::post('user/praise_handle' , 'User@praiseHandle');
            Route::post('user/record' , 'User@record');
            Route::post('user/create_and_join_collection_group' , 'User@createAndJoinCollectionGroup');
            Route::post('user/create_collection_group' , 'User@createCollectionGroup');
            Route::post('user/join_collection_group' , 'User@joinCollectionGroup');
            Route::delete('user/destroy_collection_group' , 'User@destroyCollectionGroup');
            Route::delete('user/destroy_all_collection_group' , 'User@destroyAllCollectionGroup');
            Route::delete('user/destroy_collection' , 'User@destroyCollection');
            Route::delete('user/destroy_history' , 'User@destroyHistory');
            Route::get('user/collection_group_with_judge' , 'User@collectionGroupWithJudge');
            Route::get('user/collection_group' , 'User@collectionGroup');
            Route::get('user_info' , 'User@info');
            Route::get('less_history' , 'User@lessHistory');
            Route::get('history' , 'User@histories');
            Route::get('less_relation_in_collection' , 'User@lessRelationInCollection');
            Route::get('less_collection_group_with_collection' , 'User@lessCollectionGroupWithCollection');
            Route::put('update_user' , 'User@update');
            Route::patch('update_user' , 'User@localUpdate');
            Route::patch('user/update_password_in_logged' , 'User@updatePasswordInLogged');
            Route::patch('user/update_collection_group' , 'User@updateCollectionGroup');

            // 关注用户
            Route::post('user/focus_handle' , 'User@focusHandle');


        });
    });