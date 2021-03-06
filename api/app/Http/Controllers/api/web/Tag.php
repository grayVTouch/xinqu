<?php


namespace App\Http\Controllers\api\web;


use App\Customize\api\web\action\TagAction;
use function api\web\error;
use function api\web\success;

class Tag extends Base
{
    public function show($id)
    {
        $param = $this->request->query();
        $param['module_id'] = $param['module_id'] ?? '';
        $res = TagAction::show($this , $id , $param);
        if ($res['code'] !== 0) {
            return error($res['message'] , $res['data'], $res['code']);
        }
        return success($res['message'] , $res['data']);
    }
}
