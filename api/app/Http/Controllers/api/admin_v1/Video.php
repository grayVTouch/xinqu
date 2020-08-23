<?php


namespace App\Http\Controllers\api\admin_v1;


use App\Customize\api\admin_v1\action\VideoAction;
use function api\admin_v1\error;
use function api\admin_v1\success;

class video extends Base
{
    public function index()
    {
        $param = $this->request->query();

        $param['name'] = $param['name'] ?? '';
        $param['user_id']      = $param['user_id'] ?? '';
        $param['module_id']    = $param['module_id'] ?? '';
        $param['category_id']          = $param['category_id'] ?? '';
        $param['video_subject_id']     = $param['video_subject_id'] ?? '';
        $param['type']         = $param['type'] ?? '';
        $param['status']       = $param['status'] ?? '';
        $param['order'] = $param['order'] ?? '';
        $param['limit'] = $param['limit'] ?? '';

        $res = VideoAction::index($this , $param);

        if ($res['code'] !== 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }

        return success($res['message'] , $res['data']);
    }

    public function update($id)
    {
        $param = $this->request->post();
        $param['name']          = $param['name'] ?? '';
        $param['user_id']       = $param['user_id'] ?? '';
        $param['module_id']     = $param['module_id'] ?? '';
        $param['category_id']   = $param['category_id'] ?? '';
        $param['type']          = $param['type'] ?? '';
        $param['video_subject_id']    = $param['video_subject_id'] ?? '';
        $param['thumb']         = $param['thumb'] ?? '';
        $param['description']   = $param['description'] ?? '';
        $param['weight']        = $param['weight'] ?? '';
        $param['index']        = $param['index'] ?? '';
        $param['view_count']    = $param['view_count'] ?? '';
        $param['praise_count']  = $param['praise_count'] ?? '';
        $param['against_count']  = $param['against_count'] ?? '';
        $param['status']        = $param['status'] ?? '';
        $param['fail_reason']   = $param['fail_reason'] ?? '';
        $param['src']           = $param['src'] ?? '';
        $param['create_time']   = $param['create_time'] ?? '';
        $res = VideoAction::update($this , $id ,$param);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function store()
    {
        $param = $this->request->post();
        $param['name']          = $param['name'] ?? '';
        $param['user_id']       = $param['user_id'] ?? '';
        $param['module_id']     = $param['module_id'] ?? '';
        $param['category_id']   = $param['category_id'] ?? '';
        $param['type']          = $param['type'] ?? '';
        $param['video_subject_id']    = $param['video_subject_id'] ?? '';
        $param['thumb']         = $param['thumb'] ?? '';
        $param['description']   = $param['description'] ?? '';
        $param['weight']        = $param['weight'] ?? '';
        $param['index']        = $param['index'] ?? '';
        $param['view_count']    = $param['view_count'] ?? '';
        $param['praise_count']  = $param['praise_count'] ?? '';
        $param['against_count']  = $param['against_count'] ?? '';
        $param['status']        = $param['status'] ?? '';
        $param['fail_reason']   = $param['fail_reason'] ?? '';
        $param['src']           = $param['src'] ?? '';
        $param['create_time']   = $param['create_time'] ?? '';
        $res = VideoAction::store($this ,$param);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function show($id)
    {
        $res = VideoAction::show($this , $id);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function destroy($id)
    {
        $res = VideoAction::destroy($this , $id);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function destroyAll()
    {
        $ids = $this->request->input('ids' , '[]');
        $ids = json_decode($ids , true);
        $res = VideoAction::destroyAll($this , $ids);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }

    public function destroyVideos()
    {
        $video_src_ids = $this->request->post('video_src_ids' , '[]');
        $video_src_ids = json_decode($video_src_ids , true);
        $res = VideoAction::destroyVideos($this , $video_src_ids);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }


    // 重新运行队列
    public function retry()
    {
        $ids = $this->request->post('ids' , []);
        $ids = json_decode($ids , true);
        $res = VideoAction::retry($this , $ids);
        if ($res['code'] != 0) {
            return error($res['message'] , $res['data'] , $res['code']);
        }
        return success($res['message'] , $res['data']);
    }
}
