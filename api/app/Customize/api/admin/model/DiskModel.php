<?php


namespace App\Customize\api\admin\model;


use Illuminate\Contracts\Pagination\Paginator;

class DiskModel extends Model
{
    protected $table = 'xq_disk';

    public static function findDefault(): ?DiskModel
    {
        return self::where('is_default' , 1)->first();
    }

    public static function findByPrefix(string $prefix = ''): ?DiskModel
    {
        return self::where('prefix' , $prefix)->first();
    }

    public static function findByPath(string $path = ''): ?DiskModel
    {
        return self::where('path' , $path)->first();
    }

    public static function countByIsDefault(int $is_default = 1): int
    {
        return self::where('is_default' , $is_default)->count();
    }

    public static function findByExcludeIdAndPrefix(int $exclude_id , string $prefix): ?DiskModel
    {
        return self::where([
                ['id' , '!=' , $exclude_id] ,
                ['prefix' , '=' , $prefix] ,
            ])
            ->first();
    }

    public static function findByExcludeIdAndPath(int $exclude_id , string $path): ?DiskModel
    {
        return self::where([
            ['id' , '!=' , $exclude_id] ,
            ['path' , '=' , $path] ,
        ])->first();
    }

    public static function setNotDefaultByExcludeId(int $id): int
    {
        return self::where('id' , '!=' , $id)
            ->update([
                'is_default' => 0 ,
            ]);
    }

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
}
