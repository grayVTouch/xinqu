<?php


namespace App\Customize\api\web\handler;


use App\Customize\api\web\model\VideoSubtitleModel;
use App\Customize\api\web\util\FileUtil;
use App\Model\Model;
use stdClass;
use function core\convert_object;

class VideoSubtitleHandler extends Handler
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
