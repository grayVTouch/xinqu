<?php


namespace App\Customize\api\admin\action;


use App\Customize\api\admin\handler\AdminHandler;
use App\Customize\api\admin\model\AdminModel;
use App\Customize\api\admin\model\AdminTokenModel;
use App\Customize\api\admin\model\SystemSettingsModel;
use App\Http\Controllers\api\admin\Base;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mews\Captcha\Facades\Captcha;
use function api\admin\get_form_error;
use function core\random;

class LoginAction extends Action
{
    public static function login(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'username' => 'required|min:4' ,
            'password' => 'required|min:4' ,
//            'captcha_code' => 'required|min:4' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        $is_enable_grapha_verify_code_for_login = SystemSettingsModel::getValueByKey('is_enable_grapha_verify_code_for_login');
        if ($is_enable_grapha_verify_code_for_login == 1) {
            if (empty($param['captcha_code'])) {
                return self::error('必要参数丢失【captcha_code】');
            }
            // 启用了图形验证码
            if (empty($param['captcha_key'])) {
                return self::error('必要参数丢失【captcha_key】');
            }
            if (!Captcha::check_api($param['captcha_code'] , $param['captcha_key'])) {
                return self::error('图形验证码错误');
            }
        }
        $user = AdminModel::findByUsername($param['username']);
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
            AdminTokenModel::insert([
                'user_id' => $user->id ,
                'token' => $token ,
                'expired' => $datetime
            ]);
            AdminModel::updateById($user->id , [
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

    public static function avatar(Base $context , array $param = [])
    {
        $validator = Validator::make($param , [
            'username' => 'required'
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        $user = AdminModel::findByUsername($param['username']);
        if (empty($user)) {
            return self::error('用户不存在' , '' , 404);
        }
        return self::success('' , $user->avatar);
    }
}
