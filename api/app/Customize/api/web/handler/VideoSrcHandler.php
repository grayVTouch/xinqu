<?php


namespace App\Customize\api\web\handler;


use App\Customize\api\web\model\VideoSrcModel;
use App\Customize\api\web\util\FileUtil;
use App\Customize\api\web\model\Model;
use stdClass;
use function core\convert_object;

class VideoSrcHandler extends Handler
{
    public static function handle(?Model $model , array $with = []): ?stdClass
    {
        if (empty($model)) {
            return null;
        }
        $res = convert_object($model);

        return $res;
    }

}
