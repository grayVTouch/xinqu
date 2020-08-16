<?php

use App\Model\AdminPermissionModel;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $res_url = rtrim(config('my.res_url') , '/');
        $datetime = date('Y-m-d H:i:s');
        AdminPermissionModel::insert([
            [
                'id'        => 1 ,
                'cn'        => '控制台' ,
                'en'        => 'Pannel' ,
                'value'     => '/pannel' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/pannel.png' ,
                'b_ico'         => $res_url . '/preset/ico/pannel.png' ,
                'weight'        => 1000 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 2 ,
                'cn'        => '后台用户' ,
                'en'        => 'Admin' ,
                'value'     => '/admin/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/admin.png' ,
                'b_ico'         => $res_url . '/preset/ico/admin.png' ,
                'weight'        => 999 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 3 ,
                'cn'        => '用户管理' ,
                'en'        => 'User' ,
                'value'     => '/user/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/user.png' ,
                'b_ico'         => $res_url . '/preset/ico/user.png' ,
                'weight'        => 998 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 4 ,
                'cn'        => '图片专题' ,
                'en'        => 'Image Subject' ,
                'value'     => '/image_subject/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/image.png' ,
                'b_ico'         => $res_url . '/preset/ico/image.png' ,
                'weight'        => 997 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 5 ,
                'cn'        => '视频管理' ,
                'en'        => 'Video' ,
                'value'     => 'video' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 0 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/video.png' ,
                'b_ico'         => $res_url . '/preset/ico/video.png' ,
                'weight'        => 996 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 6 ,
                'cn'        => '个性标签' ,
                'en'        => 'Tag' ,
                'value'     => '/tag/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/tag.png' ,
                'b_ico'         => $res_url . '/preset/ico/tag.png' ,
                'weight'        => 995 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 7 ,
                'cn'        => '内容分类' ,
                'en'        => 'Category' ,
                'value'     => '/category/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/category.png' ,
                'b_ico'         => $res_url . '/preset/ico/category.png' ,
                'weight'        => 994 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 8 ,
                'cn'        => '关联主体' ,
                'en'        => 'Subject' ,
                'value'     => '/subject/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/subject.png' ,
                'b_ico'         => $res_url . '/preset/ico/subject.png' ,
                'weight'        => 993 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 9 ,
                'cn'        => '模块管理' ,
                'en'        => 'Module' ,
                'value'     => '/module/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/module.png' ,
                'b_ico'         => $res_url . '/preset/ico/module.png' ,
                'weight'        => 992 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 10 ,
                'cn'        => '权限管理' ,
                'en'        => 'Permission' ,
                'value'     => '/admin_permission/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 0 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/permission.png' ,
                'b_ico'         => $res_url . '/preset/ico/permission.png' ,
                'weight'        => 991 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 11 ,
                'cn'        => '系统管理' ,
                'en'        => 'System' ,
                'value'     => 'system' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 0 ,
                'enable'    => 1 ,
                'p_id'      => 0 ,
                's_ico'         => $res_url . '/preset/ico/system.png' ,
                'b_ico'         => $res_url . '/preset/ico/system.png' ,
                'weight'        => 990 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 12 ,
                'cn'        => '视频系列' ,
                'en'        => '' ,
                'value'     => '/video_series/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 5 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 989 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 13 ,
                'cn'        => '视频制作公司' ,
                'en'        => '' ,
                'value'     => '/video_company/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 5 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 988 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 14 ,
                'cn'        => '视频专题' ,
                'en'        => '' ,
                'value'     => '/video_subject/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 5 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 987 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 15 ,
                'cn'        => '视频列表' ,
                'en'        => '' ,
                'value'     => '/video/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 5 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 986 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 16 ,
                'cn'        => '角色列表' ,
                'en'        => '' ,
                'value'     => '/role/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 10 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 985 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 17 ,
                'cn'        => '权限列表' ,
                'en'        => '' ,
                'value'     => '/admin_permission/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 10 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 984 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 18 ,
                'cn'        => '存储管理' ,
                'en'        => '' ,
                'value'     => '/disk/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 11 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 983 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 19 ,
                'cn'        => '导航菜单' ,
                'en'        => '' ,
                'value'     => '/nav/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 11 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 982 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 20 ,
                'cn'        => '系统位置' ,
                'en'        => '' ,
                'value'     => '/position/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 11 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 981 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
            [
                'id'        => 21 ,
                'cn'        => '定点图片' ,
                'en'        => '' ,
                'value'     => '/image_at_position/index' ,
                'description' => '' ,
                'type'      => 'view' ,
                'method'    => 'GET' ,
                'is_menu'   => 1 ,
                'is_view'   => 1 ,
                'enable'    => 1 ,
                'p_id'      => 11 ,
                's_ico'         => '' ,
                'b_ico'         => '' ,
                'weight'        => 980 ,
                'update_time'   => $datetime ,
                'create_time'   => $datetime ,
            ] ,
        ]);
    }
}
