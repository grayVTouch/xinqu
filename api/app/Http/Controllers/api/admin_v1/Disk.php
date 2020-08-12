<?php


namespace App\Http\Controllers\api\admin_v1;


use App\Customize\api\admin_v1\action\DiskAction;
use function api\admin_v1\error;
use function api\admin_v1\success;

class Disk extends Base
{
    public function index()
    {
        $param = $this->request->query();
        $param['name'] = $param['name'] ?? '';
        $param['order'] = $param['order'] ?? '';
        $param['limit'] = $param['limit'] ?? '';
        $res = DiskAction::index($this , $param);
        if ($res['code'] !== 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function localUpdate($id)
    {
        $param = $this->request->post();
        $param['path']          = $param['path'] ?? '';
        $param['os']            = $param['os'] ?? '';
        $param['prefix']        = $param['prefix'] ?? '';
        $param['default']       = $param['default'] ?? '';
        $res = DiskAction::localUpdate($this , $id ,$param);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function update($id)
    {
        $param = $this->request->post();
        $param['path']          = $param['path'] ?? '';
        $param['os']            = $param['os'] ?? '';
        $param['prefix']        = $param['prefix'] ?? '';
        $param['default']       = $param['default'] ?? '';
        $res = DiskAction::update($this , $id ,$param);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function store()
    {
        $param  = $this->request->post();
        $param['path']          = $param['path'] ?? '';
        $param['os']            = $param['os'] ?? '';
        $param['prefix']        = $param['prefix'] ?? '';
        $param['default']       = $param['default'] ?? '';
        $res = DiskAction::store($this ,$param);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function show($id)
    {
        $res = DiskAction::show($this , $id);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function destroy($id)
    {
        $res = DiskAction::destroy($this , $id);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function destroyAll()
    {
        $ids = $this->request->input('ids' , '[]');
        $ids = json_decode($ids , true);
        $res = DiskAction::destroyAll($this , $ids);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }
}
