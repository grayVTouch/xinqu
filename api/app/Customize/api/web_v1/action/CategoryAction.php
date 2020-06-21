<?php


namespace App\Customize\api\web_v1\action;

use App\Customize\api\web_v1\handler\CategoryHandler;
use App\Customize\api\web_v1\model\CategoryModel;
use App\Http\Controllers\api\web_v1\Base;
use Core\Lib\Category;
use function core\obj_to_array;

class CategoryAction extends Action
{
    public static function all(Base $context , array $param = [])
    {
        $res = CategoryModel::getAll();
        $res = CategoryHandler::handleAll($res);
        $res = obj_to_array($res);
        $res = Category::childrens(0 , $res , null , false ,false);
        return self::success($res);
    }
}