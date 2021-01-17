<?php


namespace App\Customize\api\web\action;

use App\Customize\api\web\handler\CollectionGroupHandler;
use App\Customize\api\web\handler\CollectionHandler;
use App\Customize\api\web\handler\FocusUserHandler;
use App\Customize\api\web\handler\HistoryHandler;
use App\Customize\api\web\handler\ImageProjectHandler;
use App\Customize\api\web\handler\UserHandler;
use App\Customize\api\web\model\CollectionGroupModel;
use App\Customize\api\web\model\CollectionModel;
use App\Customize\api\web\model\EmailCodeModel;
use App\Customize\api\web\model\FocusUserModel;
use App\Customize\api\web\model\HistoryModel;
use App\Customize\api\web\model\ImageProjectModel;
use App\Customize\api\web\model\ModuleModel;
use App\Customize\api\web\model\PraiseModel;
use App\Customize\api\web\model\UserModel;
use App\Customize\api\web\model\UserTokenModel;
use App\Customize\api\web\util\CollectionGroupUtil;
use App\Http\Controllers\api\web\Base;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mews\Captcha\Facades\Captcha;
use function api\web\get_form_error;
use function api\web\my_config;
use function api\web\user;
use function core\array_unit;
use function core\current_datetime;
use function core\random;

class UserAction extends Action
{

    public static function destroyCollectionGroup(Base $context , array $param = []): array
    {
        $param['collection_group_ids'] = [$param['collection_group_id']];
        return self::destroyAllCollectionGroup($context , $param);
    }

    public static function destroyAllCollectionGroup(Base $context , array $param = []): array
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'collection_group_ids' => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $collection_group_ids = empty($param['collection_group_ids']) ? [] : json_decode($param['collection_group_ids'] , true);
        if (empty($collection_group_ids)) {
            return self::error('请提供待删除的收藏夹');
        }
        $user = user();
        try {
            DB::beginTransaction();
            CollectionGroupModel::delByModuleIdAndUserIdAndIds($module->id , $user->id , $collection_group_ids);
            CollectionModel::delByModuleIdAndUserIdAndCollectionGroupIds($module->id , $user->id , $collection_group_ids);
            DB::commit();
            return self::success('操作成功');
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function destroyCollection(Base $context , array $param = []): array
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'collection_id' => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $user = user();
        $res = CollectionModel::delByModuleIdAndUserIdAndId($module->id , $user->id , $param['collection_id']);
        return self::success('' , $res);
    }

    public static function store(Base $context , array $param = []): array
    {
        $validator = Validator::make($param , [
            'email' => 'required|email' ,
            'email_code' => 'required' ,
            'password' => 'required|min:6' ,
            'confirm_password' => 'required|min:6' ,
//            'captcha_code' => 'required|min:4' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
//        if (empty($param['captcha_key'])) {
//            return self::error('必要参数丢失【captcha_key】');
//        }
//        if (!Captcha::check_api($param['captcha_code'] , $param['captcha_key'])) {
//            return self::error([
//                'captcha_code' => '图形验证码错误',
//            ]);
//        }
        $user = UserModel::findByEmail($param['email']);
        if (!empty($user)) {
            return self::error('邮箱已经注册过，请登录');
        }
        if ($param['password'] !== $param['confirm_password']) {
            return self::error('两次输入的密码不一致');
        }
        // 检查验证码是否正确
        $email_code = EmailCodeModel::findByEmailAndType($param['email'] , 'register');
        if (empty($email_code)) {
            return self::error('请先发送邮箱验证码');
        }
        $timestamp = time();
        $code_duration = my_config('app.code_duration');
        $expired_timestamp = strtotime($email_code->send_time) + $code_duration;
        if ($email_code->used || $timestamp > $expired_timestamp) {
            return self::error('邮箱验证码已经失效，请重新发送');
        }
        if ($email_code->code !== $param['email_code']) {
            return self::error('验证码错误');
        }
        $token = random(32 , 'mixed' , true);
        $datetime = date('Y-m-d H:i:s' , time() + 7 * 24 * 3600);
        try {
            DB::beginTransaction();
            $id = UserModel::insertGetId([
                'username' => random(6 , 'letter' , true) ,
                'email' => $param['email'] ,
                'password' => Hash::make($param['password']) ,
                'last_time' => date('Y-m-d H:i:s'),
                'last_ip'   => $context->request->ip(),
            ]);
            UserTokenModel::insertGetId([
                'user_id' => $id ,
                'token' => $token ,
                'expired' => $datetime
            ]);
            EmailCodeModel::updateById($email_code->id , [
               'used' => 1 ,
            ]);
            DB::commit();
            return self::success('' , $token);
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function login(Base $context , array $param = []): array
    {
        $validator = Validator::make($param , [
            'username' => 'required|min:6' ,
            'password' => 'required|min:6' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $user = UserModel::findByValueInUsernameOrEmailOrPhone($param['username']);
        if (empty($user)) {
            return self::error('用户不存在');
        }
        if (!Hash::check($param['password'] , $user->password)) {
            return self::error('密码错误');
        }
        $token = random(32 , 'mixed' , true);
        $datetime = date('Y-m-d H:i:s' , time() + 7 * 24 * 3600);
        try {
            DB::beginTransaction();
            UserTokenModel::insert([
                'user_id' => $user->id ,
                'token' => $token ,
                'expired' => $datetime
            ]);
            UserModel::updateById($user->id , [
                'last_time' => date('Y-m-d H:i:s'),
                'last_ip'   => $context->request->ip(),
            ]);
            DB::commit();
            return self::success('' , $token);
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function info(Base $context , array $param = [])
    {
        $user = user();
        if (empty($user)) {
            return self::error('用户尚未登录' , '' , 401);
        }
        return self::success('' , $user);
    }

    public static function updatePassword(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'email' => 'required|min:6' ,
            'email_code' => 'required' ,
            'password' => 'required|min:6' ,
            'confirm_password' => 'required|min:6' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $user = UserModel::findByEmail($param['email']);
        if (empty($user)) {
            return self::error('邮箱尚未注册，请先注册');
        }
        if ($param['password'] !== $param['confirm_password']) {
            return self::error('两次输入的密码不一致');
        }
        // 检查验证码是否正确
        $email_code = EmailCodeModel::findByEmailAndType($user->email , 'password');
        if (empty($email_code)) {
            return self::error('请先发送邮箱验证码');
        }
        $timestamp = time();
        $code_duration = my_config('app.code_duration');
        $expired_timestamp = strtotime($email_code->send_time) + $code_duration;
        if ($email_code->used || $timestamp > $expired_timestamp) {
            return self::error('邮箱验证码已经失效，请重新发送');
        }
        if ($email_code->code !== $param['email_code']) {
            return self::error('验证码错误');
        }
        try {
            DB::beginTransaction();
            UserModel::updateById($user->id , [
                'password' => Hash::make($param['password'])
            ]);
            EmailCodeModel::updateById($email_code->id , [
                'used' => 1 ,
            ]);
            DB::commit();
            return self::success('操作成功');
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function lessHistory(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id'             => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $user = user();
        $limit = $param['limit'] ? $param['limit'] : my_config('app.limit');
        $res = HistoryModel::getOrderTimeByModuleIdAndUserIdAndLimit($module->id , $user->id , $limit);
        $res = HistoryHandler::handleAll($res);
        $date = date('Y-m-d');
        $yesterday = date_create('yesterday')->format('Y-m-d');
        $groups = [];
        $findIndex = function($name) use(&$groups): int
        {
            foreach ($groups as $k => $v)
            {
                if ($v['name'] === $name) {
                    return $k;
                }
            }
            return -1;
        };
        foreach ($res as $v)
        {
            switch ($v->date)
            {
                case $date:
                    $name = '今天';
                    break;
                case $yesterday:
                    $name = '昨天';
                    break;
                default:
                    $name = $v->date;
            }
            $index = $findIndex($name);
            if ($index < 0) {
                $groups[] = [
                    'name' => $name ,
                    'data' => [] ,
                ];
                $index = count($groups) - 1;
            }
            $groups[$index]['data'][] = $v;
        }
        return self::success('' , $groups);
    }

    public static function histories(Base $context , array $param = []): array
    {
        $relation_type_range = my_config_keys('business.relation_type_for_history');
        $validator = Validator::make($param , [
            'module_id'             => 'required|integer' ,
            'relation_type' => ['sometimes' , Rule::in($relation_type_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $user = user();
        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = HistoryModel::getByModuleIdAndUserIdAndRelationTypeAndValueAndLimit($module->id , $user->id , $param['relation_type'] , $param['value'] ,$limit);
        $res = HistoryHandler::handlePaginator($res);
        // 对时间进行分组
        $date = date('Y-m-d');
        $yesterday = date_create('yesterday')->format('Y-m-d');
        $groups = [];
        $findIndex = function($name) use(&$groups): int
        {
            foreach ($groups as $k => $v)
            {
                if ($v['name'] === $name) {
                    return $k;
                }
            }
            return -1;
        };
        foreach ($res->data as $v)
        {
            switch ($v->date)
            {
                case $date:
                    $name = '今天';
                    break;
                case $yesterday:
                    $name = '昨天';
                    break;
                default:
                    $name = $v->date;
            }
            $index = $findIndex($name);
            if ($index < 0) {
                $groups[] = [
                    'name' => $name ,
                    'data' => [] ,
                ];
                $index = count($groups) - 1;
            }
            $groups[$index]['data'][] = $v;
        }
        $res->data = $groups;
        return self::success('' , $res);
    }


    public static function collectionGroupWithJudge(Base $context , array $param = []): array
    {
        $relation_type_range = my_config_keys('business.relation_type_for_collection');
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'relation_type' => ['required' , Rule::in($relation_type_range)] ,
            'relation_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        switch ($param['relation_type'])
        {
            case 'image_project':
                $relation = ImageProjectModel::find($param['relation_id']);
                break;
            default:
                $relation = null;
        }
        if (empty($relation)) {
            return self::error('关联事物不存在');
        }
        $user = user();
        $res = CollectionGroupModel::getByModuleIdAndUserIdAndValue($module->id , $user->id);
        $res = CollectionGroupHandler::handleAll($res);
        foreach ($res as $v)
        {
            CollectionGroupUtil::handle($v , $param['relation_type'] , $relation->id);
        }
        return self::success('' , $res);
    }

    public static function collectionGroup(Base $context , array $param = []): array
    {
        $relation_type_range = my_config_keys('business.relation_type_for_collection');
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'relation_type' => ['sometimes' , Rule::in($relation_type_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $user = user();
        $res = CollectionGroupModel::getByModuleIdAndUserIdAndRelationTypeAndValue($module->id , $user->id , $param['relation_type'] ,  $param['value']);
        $res = CollectionGroupHandler::handleAll($res);
        return self::success('' , $res);
    }

    public static function collectionHandle(Base $context , array $param = [])
    {
        $relation_type_range = my_config_keys('business.relation_type_for_collection');
        $action_range = my_config_keys('business.bool_for_int');
        $validator = Validator::make($param , [
            'module_id'             => 'required|integer' ,
            'collection_group_id'   => 'required|integer' ,
            'action'                => ['required' , Rule::in($action_range)] ,
            'relation_type' => ['required' , Rule::in($relation_type_range)] ,
            'relation_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $collection_group = CollectionGroupModel::find($param['collection_group_id']);
        if (empty($collection_group)) {
            return self::error('收藏夹不存在' , '' , 404);
        }
        $user = user();
        $res = null;
        if ($param['relation_type'] === 'image_project') {
            $relation = ImageProjectModel::find($param['relation_id']);
            if (empty($relation)) {
                return self::error('关联事物不存在');
            }
            if ($param['action'] == 1) {
                // 收藏
                CollectionModel::insertOrIgnore([
                    'module_id' => $module->id ,
                    'user_id' => $user->id ,
                    'collection_group_id' => $collection_group->id ,
                    'relation_type' => $param['relation_type'] ,
                    'relation_id' => $relation->id ,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // 取消收藏
                CollectionModel::delByModuleIdAndUserIdAndCollectionGroupIdAndRelationTypeAndRelationId($module->id , $user->id , $collection_group->id , 'image_project' , $relation->id);
            }
        } else {
            // 其他类型，预留
        }
        $collection_group = CollectionGroupHandler::handle($collection_group);
        CollectionGroupUtil::handle($collection_group , $param['relation_type'] , $relation->id);
        return self::success('' , $collection_group);
    }

    //
    public static function praiseHandle(Base $context , array $param = [])
    {
        $relation_type_range = my_config_keys('business.relation_type_for_praise');
        $action_range = my_config_keys('business.bool_for_int');
        $validator = Validator::make($param , [
            'module_id'             => 'required|integer' ,
            'action'                => ['required' , Rule::in($action_range)] ,
            'relation_type' => ['required' , Rule::in($relation_type_range)] ,
            'relation_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        if ($param['relation_type'] === 'image_project') {
            // 图片专题
            $relation = ImageProjectModel::find($param['relation_id']);
            if (empty($relation)) {
                return self::error('图片专题不存在');
            }
            $user = user();
            if ($param['action'] == 1) {
                // 点赞
                PraiseModel::insertOrIgnore([
                    'module_id' => $module->id ,
                    'user_id' => $user->id ,
                    'relation_type' => $param['relation_type'] ,
                    'relation_id' => $relation->id ,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                ImageProjectModel::countHandle($relation->id , 'praise_count' , 'increment');
            } else {
                // 取消收藏
                PraiseModel::delByModuleIdAndUserIdAndRelationTypeAndRelationId($module->id , $user->id , 'image_project' , $relation->id);
                ImageProjectModel::countHandle($relation->id , 'praise_count' , 'decrement');
            }
            $res = ImageProjectModel::find($relation->id);
            $res = ImageProjectHandler::handle($res);
        } else {
            // 其他类型，预留
        }
        return self::success('' , $res);
    }

    public static function record(Base $context , array $param = [])
    {
        $relation_type_range = my_config_keys('business.relation_type_for_history');
        $validator = Validator::make($param , [
            'module_id'             => 'required|integer' ,
            'relation_type' => ['required' , Rule::in($relation_type_range)] ,
            'relation_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        switch ($param['relation_type'])
        {
            case 'image_project':
                $relation = ImageProjectModel::find($param['relation_id']);
                break;
        }
        if (empty($relation)) {
            return self::error('关联事物不存在' , '' , 404);
        }
        if ($module->id !== $relation->module_id) {
            return self::error('禁止记录不同模块的内容' , '' , 403);
        }
        $user = user();
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $res = HistoryModel::updateOrInsert([
            'module_id' => $module->id ,
            'user_id' => $user->id ,
            'relation_type' => $param['relation_type'] ,
            'relation_id' => $relation->id ,
            'date' => date('Y-m-d') ,
        ] , [
            'time' => date('H:i:s') ,
            'created_at' => $date . ' ' . $time ,
        ]);
        return self::success('' , $res);
    }

    public static function createAndJoinCollectionGroup(Base $context , array $param = [])
    {
        $relation_type_range = my_config_keys('business.relation_type_for_collection');
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'relation_type' => ['required' , Rule::in($relation_type_range)] ,
            'relation_id' => 'required|integer' ,
            'name'      => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        switch ($param['relation_type'])
        {
            case 'image_project':
                $relation = ImageProjectModel::find($param['relation_id']);
                break;
            default:
                $relation = null;
        }
        if (empty($relation)) {
            return self::error('关联的事物不存在' , '' , 404);
        }
        if ($module->id !== $relation->module_id) {
            return self::error('禁止记录不同模块的内容' , '' , 403);
        }
        $user = user();
        $collection_group = CollectionGroupModel::findByModuleIdAndUserIdAndName($module->id , $user->id , $param['name']);
        if (!empty($collection_group)) {
            return self::error('收藏夹已经存在');
        }
        try {
            DB::beginTransaction();
            $id = CollectionGroupModel::insertGetId([
                'module_id' => $module->id ,
                'user_id' => $user->id ,
                'name' => $param['name'] ,
                'created_at' => current_datetime() ,
            ]);
            CollectionModel::insertGetId([
                'module_id' => $module->id ,
                'user_id' => $user->id ,
                'collection_group_id' => $id ,
                'relation_type' => $param['relation_type'] ,
                'relation_id' => $relation->id ,
                'created_at' => current_datetime() ,
            ]);
            $collection_group = CollectionGroupModel::find($id);
            $collection_group = CollectionGroupHandler::handle($collection_group);
            CollectionGroupUtil::handle($collection_group , $param['relation_type'] , $relation->id);
            DB::commit();
            return self::success('' , $collection_group);
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function createCollectionGroup(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'name'      => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $user = user();
        $collection_group = CollectionGroupModel::findByModuleIdAndUserIdAndName($module->id , $user->id , $param['name']);
        if (!empty($collection_group)) {
            return self::error('收藏夹已经存在');
        }
        $res = CollectionGroupModel::insertGetId([
            'module_id' => $module->id ,
            'user_id' => $user->id ,
            'name' => $param['name'] ,
            'created_at' => current_datetime() ,
        ]);
        return self::success('' , $res);
    }

    public static function joinCollectionGroup(Base $context , array $param = [])
    {
        $relation_type_range = my_config_keys('business.relation_type_for_collection');
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'relation_type' => ['required' , Rule::in($relation_type_range)] ,
            'relation_id' => 'required|integer' ,
            'collection_group_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        switch ($param['relation_type'])
        {
            case 'image_project':
                $relation = ImageProjectModel::find($param['relation_id']);
                break;
            default:
                $relation = null;
        }
        if (empty($relation)) {
            return self::error('关联的事物不存在' , '' , 404);
        }
        if ($module->id !== $relation->module_id) {
            return self::error('禁止记录不同模块的内容' , '' , 403);
        }
        $user = user();
        $collection_group = CollectionGroupModel::find($param['collection_group_id']);
        if (empty($collection_group)) {
            return self::error('收藏夹不存在' , '' , 404);
        }
        CollectionModel::insertGetId([
            'module_id' => $module->id ,
            'user_id' => $user->id ,
            'collection_group_id' => $collection_group->id ,
            'relation_type' => $param['relation_type'] ,
            'relation_id' => $relation->id ,
            'created_at' => current_datetime() ,
        ]);
        $collection_group = CollectionGroupHandler::handle($collection_group);
        CollectionGroupUtil::handle($collection_group , $param['relation_type'] , $relation->id);
        return self::success('' , $collection_group);
    }

    public static function lessRelationInCollection(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'collection_group_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $collection_group = CollectionGroupModel::find($param['collection_group_id']);
        if (empty($collection_group)) {
            return self::error('收藏夹不存在' , '' , 404);
        }
        $limit = $param['limit'] ? $param['limit'] : my_config('app.limit');
        $user = user();
        $res = CollectionModel::getByModuleIdAndUserIdAndCollectionGroupIdAndLimit($module->id , $user->id , $collection_group->id , $limit);
        $res = CollectionHandler::handleAll($res);
        return self::success('' , $res);
    }

    public static function lessCollectionGroupWithCollection(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $collection_limit = $param['collection_limit'] ? $param['collection_limit'] : my_config('app.limit');
        $relation_limit = $param['relation_limit'] ? $param['relation_limit'] : my_config('app.limit');
        $user = user();
        $total_collection_group = CollectionGroupModel::countByModuleIdAndUserId($module->id , $user->id);
        $collection_group = CollectionGroupModel::getByModuleIdAndUserIdAndLimit($module->id , $user->id , $collection_limit);
        $collection_group = CollectionGroupHandler::handleAll($collection_group);
        foreach ($collection_group as $v)
        {
            $collections = CollectionModel::getByModuleIdAndUserIdAndCollectionGroupIdAndLimit($module->id , $user->id , $v->id , $relation_limit);
            $collections = CollectionHandler::handleAll($collections);
            $v->collections = $collections;
        }
        return self::success('' , [
            'total_collection_group' => $total_collection_group ,
            'collection_groups' => $collection_group ,
        ]);
    }

    public static function update(Base $context , array $param = [])
    {
        $sex_range = my_config_keys('business.sex');
        $validator = Validator::make($param , [
            'sex' => ['required' , Rule::in($sex_range)] ,
            'email' => 'sometimes|email' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $user = user();
        UserModel::updateById($user->id , array_unit($param , [
            'nickname' ,
            'sex' ,
            'avatar' ,
            'phone' ,
            'email' ,
            'description' ,
            'birthday' ,
            'channel_thumb' ,
        ]));
        $user = UserModel::find($user->id);
        $user = UserHandler::handle($user);
        return self::success('' , $user);
    }


    public static function updatePasswordInLogged(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'old_password' => 'required' ,
            'password'      => 'required|min:6' ,
            'confirm_password' => 'required|min:6' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $user = user();
        if (!Hash::check($param['old_password'] , $user->password)) {
            return self::error('原密码错误');
        }
        if ($param['password'] !== $param['confirm_password']) {
            return self::error('两次输入的密码不一致');
        }
        $password = Hash::make($param['password']);
        UserModel::updateById($user->id , [
            'password' => $password
        ]);
        return self::success('操作成功');
    }

    public static function destroyHistory(Base $context , array $param = []): array
    {
        $validator = Validator::make($param , [
            'module_id' => 'required' ,
            'history_ids'      => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $history_ids = empty($param['history_ids']) ? [] : json_decode($param['history_ids'] , true);
        if (empty($history_ids)) {
            return self::error('请提供待删除的项');
        }
        $user = user();
        $histories = HistoryModel::getByModuleIdAndUserIdAndIds($module->id , $user->id , $history_ids);
        if (count($history_ids) !== count($histories)) {
            return self::error('存在无效记录，请重新选择');
        }
        // 检查记录是否是当前登录用户
        $count = HistoryModel::destroy($history_ids);
        return self::success('操作成功' , $count);
    }

    public static function collections(Base $context , array $param = []): array
    {
        $relation_type_range = my_config_keys('business.relation_type_for_collection');
        $validator = Validator::make($param , [
            'module_id' => 'required' ,
            'collection_group_id' => 'required' ,
            'relation_type' => ['sometimes' , Rule::in($relation_type_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $collection_group = CollectionGroupModel::find($param['collection_group_id']);
        if (empty($collection_group)) {
            return self::error('收藏夹不存在' , '' , 404);
        }
        $limit = empty($param['limit']) ? my_config('app.limit') : $param['limit'];
        $res = CollectionModel::getWithPagerByModuleIdAndUserIdAndCollectionGroupIdAndLimit($module->id , $collection_group->user_id , $collection_group->id , $param['relation_type'] , $limit);
        $res = CollectionHandler::handlePaginator($res);
        return self::success('' , $res);
    }

    public static function updateCollectionGroup(Base $context , array $param = []): array
    {
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'collection_group_id' => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $collection_group = CollectionGroupModel::find($param['collection_group_id']);
        if (empty($collection_group)) {
            return self::error('收藏夹不存在');
        }
        $user = user();
        if (!empty(CollectionGroupModel::findByModuleIdAndUserIdAndNameExcludeIds($module->id , $user->id , $param['name'] , [$collection_group->id]))) {
            return self::error('名称已经被使用');
        }
        CollectionGroupModel::updateById($collection_group->id , [
            'name' => $param['name']
        ]);
        $res = CollectionGroupModel::find($collection_group->id);
        $res = CollectionGroupHandler::handle($res);
        return self::success('' , $res);
    }

    public static function focusHandle(Base $context , array $param = []): array
    {
        $bool_range = my_config_keys('business.bool_for_int');
        $validator = Validator::make($param , [
            'user_id' => 'required|integer' ,
            'action' => ['required' , Rule::in($bool_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $focus_user = UserModel::find($param['user_id']);
        if (empty($focus_user)) {
            return self::error('用户不存在' , '' , 404);
        }
        $user = user();
        if ($user->id === $focus_user->id) {
            return self::error('禁止关注自己' , '' , 403);
        }
        if ($param['action'] == 1) {
            // 关注用户
            $res = FocusUserModel::findByUserIdAndFocusUserId($user->id , $focus_user->id);
            if ($res) {
                return self::error('您已经关注该用户' , 403);
            }
            FocusUserModel::insertGetId([
                'user_id' => $user->id ,
                'focus_user_id' => $focus_user->id ,
                'created_at' => current_datetime() ,
            ]);
        } else {
            // 取消关注
            FocusUserModel::delByUserIdAndFocusUserId($user->id , $focus_user->id);
        }
        return self::success('操作成功');
    }

    public static function myFocusUser(Base $context , int $user_id , array $param = []): array
    {
        $user = UserModel::find($user_id);
        if (empty($user)) {
            return self::error('用户不存在' , '' , 404);
        }
        $limit = empty($param['limit']) ? $param['limit'] : my_config('app.limit');
        $res = FocusUserModel::getWithPagerByUserIdAndLimit($user->id , $limit);
        $res = FocusUserHandler::handlePaginator($res);
        return self::success('' , $res);
    }

    public static function focusMeUser(Base $context , int $user_id , array $param = []): array
    {
        $user = UserModel::find($user_id);
        if (empty($user)) {
            return self::error('用户不存在' , '' , 404);
        }
        $limit = empty($param['limit']) ? $param['limit'] : my_config('app.limit');
        $res = FocusUserModel::getWithPagerByFocusUserIdAndLimit($user->id , $limit);
        $res = FocusUserHandler::handlePaginator($res);
        return self::success('' , $res);
    }

    public static function show(Base $context , int $user_id , array $param = []): array
    {
        $user = UserModel::find($user_id);
        if (empty($user)) {
            return self::error('用户不存在' , '' , 404);
        }
        $user = UserHandler::handle($user);
        return self::success('' , $user);
    }

    public static function collectionGroupByUserId(Base $context , int $user_id , array $param = []): array
    {
        $user = UserModel::find($user_id);
        if (empty($user)) {
            return self::error('用户不存在' , '' , 404);
        }
        $relation_type_range = my_config_keys('business.relation_type_for_collection');
        $validator = Validator::make($param , [
            'module_id' => 'required|integer' ,
            'relation_type' => ['sometimes' , Rule::in($relation_type_range)] ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first());
        }
        $module = ModuleModel::find($param['module_id']);
        if (empty($module)) {
            return self::error('模块不存在');
        }
        $res = CollectionGroupModel::getByModuleIdAndUserIdAndRelationTypeAndValue($module->id , $user->id , $param['relation_type'] ,  $param['value']);
        $res = CollectionGroupHandler::handleAll($res);
        return self::success('' , $res);
    }

    // 局部更新
    public static function localUpdate(Base $context , array $param = [])
    {
        $sex_range = my_config_keys('business.sex');
        $validator = Validator::make($param , [
            'sex' => ['sometimes' , Rule::in($sex_range)] ,
            'email' => 'sometimes|email' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , get_form_error($validator));
        }
        $user = user();
        $param['nickname']  = empty($param['nickname']) ? $user->nickname : $param['nickname'];
        $param['sex']       = $param['sex'] === '' ? $user->sex : $param['sex'];
        $param['avatar']    = $param['avatar'] === '' ? $user->avatar : $param['avatar'];
        $param['phone']     = $param['phone'] === '' ? $user->phone : $param['phone'];
        $param['email']     = $param['email'] === '' ? $user->email : $param['email'];
        $param['description']   = $param['description'] === '' ? $user->description : $param['description'];
        $param['birthday']      = empty($param['birthday']) ? $user->birthday : $param['birthday'];
        $param['channel_thumb'] = $param['channel_thumb'] === '' ? $user->channel_thumb : $param['channel_thumb'];
        UserModel::updateById($user->id , array_unit($param , [
            'nickname' ,
            'sex' ,
            'avatar' ,
            'phone' ,
            'email' ,
            'description' ,
            'birthday' ,
            'channel_thumb' ,
        ]));
        $user = UserModel::find($user->id);
        $user = UserHandler::handle($user);
        return self::success('' , $user);
    }


    // 局部更新
    public static function collectionGroupInfo(Base $context , int $collection_group_id , array $param = []): array
    {
        $collection_group = CollectionGroupModel::find($collection_group_id);
        if (empty($collection_group)) {
            return self::error('记录不存在' , '' , 404);
        }
        $collection_group = CollectionGroupHandler::handle($collection_group);
        return self::success('' , $collection_group);
    }
}
