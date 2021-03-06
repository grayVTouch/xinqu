<?php


namespace App\Http\Controllers\api\admin;


use App\Customize\api\admin\action\UserAction;
use function api\admin\error;
use function api\admin\success;

class User extends Base
{
    public function search()
    {
        $param = $this->request->query();
        $param['value'] = $param['value'] ?? '';
        $param['size'] = $param['size'] ?? '';
        $res = UserAction::search($this , $param);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function index()
    {
        $param = $this->request->query();

        $param['username']  = $param['username'] ?? '';
        $param['nickname']  = $param['nickname'] ?? '';
        $param['sex']       = $param['sex'] ?? '';
        $param['phone']     = $param['phone'] ?? '';
        $param['email']     = $param['email'] ?? '';
        $param['order']     = $param['order'] ?? '';
        $param['size']     = $param['size'] ?? '';

        $res = UserAction::index($this , $param);

        if ($res['code'] !== 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }

        return success($res['message'] , $res['data']);
    }

    public function update($id)
    {
        $param = $this->request->post();
        $param['username']      = $param['username'] ?? '';
        $param['nickname']      = $param['nickname'] ?? '';
        $param['password']  = $param['password'] ?? '';
        $param['sex']       = $param['sex'] ?? '';
        $param['birthday']  = $param['birthday'] ?? '';
        $param['avatar']    = $param['avatar'] ?? '';
        $param['phone']     = $param['phone'] ?? '';
        $param['email']     = $param['email'] ?? '';
        $param['user_group_id'] = $param['user_group_id'] ?? '';
        $param['description'] = $param['description'] ?? '';
        $res = UserAction::update($this , $id ,$param);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function store()
    {
        $param = $this->request->post();
        $param['username']      = $param['username'] ?? '';
        $param['nickname']      = $param['nickname'] ?? '';
        $param['password']  = $param['password'] ?? '';
        $param['sex']       = $param['sex'] ?? '';
        $param['birthday']  = $param['birthday'] ?? '';
        $param['avatar']    = $param['avatar'] ?? '';
        $param['phone']     = $param['phone'] ?? '';
        $param['email']     = $param['email'] ?? '';
        $param['user_group_id'] = $param['user_group_id'] ?? '';
        $param['description'] = $param['description'] ?? '';
        $res = UserAction::store($this ,$param);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function show($id)
    {
        $res = UserAction::show($this , $id);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function destroy($id)
    {
        $res = UserAction::destroy($this , $id);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function destroyAll()
    {
        $ids = $this->request->input('ids' , '[]');
        $ids = json_decode($ids , true);
        $res = UserAction::destroyAll($this , $ids);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }
}
