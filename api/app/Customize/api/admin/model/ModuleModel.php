<?php


namespace App\Customize\api\admin\model;


use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ModuleModel extends Model
{
    protected $table = 'xq_module';

    public static function index(array $filter = [] , array $order = [] , int $size = 20): Paginator
    {
        $filter['id'] = $filter['id'] ?? '';
        $filter['name'] = $filter['name'] ?? '';
        $order['field'] = $order['field'] ?? 'id';
        $order['value'] = $order['value'] ?? 'desc';
        $where = [];
        if ($filter['id'] !== '') {
            $where[] = ['id' , '=' , $filter['id']];
        }
        if ($filter['name'] !== '') {
            $where[] = ['name' , 'like' , "%{$filter['name']}%"];
        }
        return self::where($where)
            ->orderBy($order['field'] , $order['value'])
            ->paginate($size);
    }

    public static function setNotDefaultByExcludeId(int $exclude_id): int
    {
        return self::where('id' , '!=' , $exclude_id)
            ->update([
                'is_default' => 0 ,
            ]);
    }

    public static function findByName(string $name): ?ModuleModel
    {
        return self::where('name' , $name)->first();
    }
}
