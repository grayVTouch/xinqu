<?php


namespace App\Customize\api\web\action;


use App\Customize\api\web\util\FileUtil;
use App\Customize\api\web\repository\ResourceRepository;
use App\Http\Controllers\api\web\Base;
use Core\Lib\ImageProcessor;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function api\web\my_config;

class FileAction extends Action
{
    // 上传文件（任意类型文件）
    public static function upload(Base $context , ?UploadedFile $file , array $param = [])
    {
        $validator = Validator::make($param , [
            'file' => 'required' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        try {
            $dir        = date('Ymd');
            $path       = FileUtil::upload($file , $dir);
            $real_path  = FileUtil::generateRealPathByWithPrefixRelativePath($path);
            $url        = FileUtil::generateUrlByRelativePath($path);
            ResourceRepository::create($url , $real_path , 'local' , 1 , 0);
            return self::success('' , $url);
        } catch(Exception $e) {
            return self::error($e->getMessage() , $e->getTraceAsString());
        }
    }

    /**
     * @param Base $context
     * @param UploadedFile|null $file
     * @param array $param
     * @return array
     * @throws \Exception
     */
    public static function uploadImage(Base $context , ?UploadedFile $file , array $param = []): array
    {
        $mode_range = my_config('business.mode_for_file');
        $validator = Validator::make($param , [
            'm' => ['sometimes' , Rule::in($mode_range)] ,
            'w' => 'sometimes|integer' ,
            'h' => 'sometimes|integer' ,
            'file' => 'required|mimes:jpg,jpeg,png,gif,webp' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        try {
            $dir        = date('Ymd');
            $path       = FileUtil::upload($file , $dir);
            $real_path  = FileUtil::generateRealPathByWithPrefixRelativePath($path);
            $url        = FileUtil::generateUrlByRelativePath($path);
            if (empty($param['m'])) {
                if (!empty($param['w'])) {
                    $mode = 'fix-width';
                    if (!empty($param['h'])) {
                        $mode = 'fix';
                    }
                } else if (!empty($param['h'])) {
                    $mode = 'fix-height';
                } else {
                    $mode = '';
                }
            } else {
                $mode = $param['m'];
            }
            if (in_array($mode , $mode_range)) {
                $o_real_path        = $real_path;
                $real_path          = FileUtil::generateRealPathByWithPrefixRelativePath($path);
                $save_dir           = FileUtil::dir('system' , $dir);
                if (!file_exists($save_dir)) {
                    // 目录不存在则创建
                    mkdir($save_dir , 0755 , true);
                }
                $image_processor    = new ImageProcessor($save_dir);
                $real_path = $image_processor->compress($real_path , [
                    'mode'      => $mode ,
                    'ratio'     => $param['r'] ,
                    'width'     => $param['w'] ,
                    'height'    => $param['h'] ,
                ] , false);
                // 删除源文件
                unlink($o_real_path);
                $url = FileUtil::generateUrlByRealPath($real_path);
            }
            ResourceRepository::create($url , $real_path , 'local' , 1 , 0);
            return self::success('' , $url);
        } catch(Exception $e) {
            return self::error($e->getMessage() , $e->getTraceAsString());
        }
    }

    // 上传视频
    public static function uploadVideo(Base $context , ?UploadedFile $file , array $param = [])
    {
        $validator = Validator::make($param , [
            'file' => 'required|mimes:mp4,mov,mkv,avi,flv,rm,rmvb,ts' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        try {
            $dir        = date('Ymd');
            $path       = FileUtil::upload($file , $dir);
            $real_path  = FileUtil::generateRealPathByWithPrefixRelativePath($path);
            $url        = FileUtil::generateUrlByRelativePath($path);
            ResourceRepository::create($url , $real_path , 'local' , 1 , 0);
            return self::success('' , $url);
        } catch(Exception $e) {
            return self::error($e->getMessage() , $e->getTraceAsString());
        }
    }

    // 上传视频字幕
    public static function uploadSubtitle(Base $context , ?UploadedFile $file , array $param = [])
    {
        $validator = Validator::make($param , [
//            'file' => 'required|mimes:ass,idx,sub,srt,vtt,ssa' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        $extension = $file->getClientOriginalExtension();
        $ext_range = ['ass' , 'idx' , 'sub' , 'srt' , 'vtt' , 'ssa'];
        if (!in_array($extension , $ext_range)) {
            return self::error('不支持的文件类型');
        }
        try {
            $dir        = date('Ymd');
            $path       = FileUtil::upload($file , $dir);
            $real_path  = FileUtil::generateRealPathByWithPrefixRelativePath($path);
            $url        = FileUtil::generateUrlByRelativePath($path);
            ResourceRepository::create($url , $real_path , 'local' , 1 , 0);
            return self::success('' , $url);
        } catch(Exception $e) {
            return self::error($e->getMessage() , $e->getTraceAsString());
        }
    }

    // 上传文档|电子表格之类
    public static function uploadOffice(Base $context , ?UploadedFile $file , array $param = [])
    {
        $validator = Validator::make($param , [
            'file' => 'required|mimes:doc,docx,xls,xlsx' ,
        ]);
        if ($validator->fails()) {
            return self::error($validator->errors()->first() , $validator->errors());
        }
        try {
            $dir        = date('Ymd');
            $path       = FileUtil::upload($file , $dir);
            $real_path  = FileUtil::generateRealPathByWithPrefixRelativePath($path);
            $url        = FileUtil::generateUrlByRelativePath($path);
            ResourceRepository::create($url , $real_path , 'local' , 1 , 0);
            return self::success('' , $url);
        } catch(Exception $e) {
            return self::error($e->getMessage() , $e->getTraceAsString());
        }
    }

}
