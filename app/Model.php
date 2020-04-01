<?php

namespace App;

use Illuminate\Database\Eloquent\Model as IlluminateModel;
use Illuminate\Support\Facades\DB;

/**
 * 模型基类
 *
 * @author bryan <voidpointers@hotmail.com>
 */
class Model extends IlluminateModel
{
    /**
     * 时间格式
     */
    protected $dateFormat = 'U';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    /**
     * 限制批量更新字段
     */
    protected $guarded = [];

    /**
     * 创建时间
     */
    const CREATED_AT = 'create_time';

    /**
     * 更新时间
     */
    const UPDATED_AT = 'update_time';

    public function filtering(array $params = [])
    {
        $fillable = $this->fillable;
        if (!$fillable) {
            return $params;
        }

        return array_filter($params, function ($key) use($fillable) {
            return in_array($key, $fillable);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function store(array $params, $uk = 'id')
    {
        $params = array_column($params, null, $uk);
        // 获取已入库数据
        $temp = static::whereIn(
            $uk, array_keys($params)
        )->pluck($uk)->all();

        $create = $update = [];

        foreach ($params as $key => $value) {
            // 过滤数据
            $value = $this->filtering($value);
            if (in_array($key, $temp)) {
                $update[] = $value;
            } else {
                $create[] = $value;
            }
        }

        if ($update) {
            $res = self::updateBatch($update, $uk, $uk);
        }
        if ($create) {
            $res = self::insert($create);
        }

        return $res;
    }

    /**
     * 批量更新
     *
     * @param array $inputs
     * @param string $where_field
     * @param string $when_field
     * @return mixed
     *
     * [['id' => 1, 'status' => 1], ['id' => 2, 'status' => 1]]
     *
     * update users set name =
     *    case
     *    when id = 1 then 'a'
     *    when id = 2 then 'b'
     * where id in (1,2);
     */
    public static function updateBatch(array $inputs, $where_field = 'id', $when_field = 'id')
    {
        if (empty($inputs)) {
            throw new \InvalidArgumentException('parameter error');
        }
        if (!($where = array_pluck($inputs, $where_field)) || !($when = array_pluck($inputs, $when_field))) {
            throw new \InvalidArgumentException('parameter error');
        }

        $when_arr = [];
        foreach ($inputs as $input) {
            $when_val = $input[$when_field];
            foreach ($input as $key => $value) {
                if ($key == $when_field) continue;
                $when_arr[$key][] = "when {$when_field} = '{$when_val}' then '{$value}'";
            }
        }

        $build = static::whereIn($where_field, $where);
        foreach ($when_arr as $key => &$item) {
            $item = DB::raw('case ' . implode(' ', $item) . ' end ');
        }

        return $build->update($when_arr);
    }
}
