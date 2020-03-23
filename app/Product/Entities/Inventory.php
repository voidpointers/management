<?php

namespace Product\Entities;

use App\Model;

class Inventory extends Model
{
    const UPDATED_AT = null;

    protected $table = 'listing_inventories';

    protected $fillable = [
        'product_id',
        'sku',
        'property_values',
        'offerings',
        'is_deleted',
        "is_enabled"
    ];

    public function store($params)
    {
        $properties = self::whereIn('product_id', array_column($params, 'product_id'))
            ->pluck('product_id')
            ->all();

        $update = $create = [];

        foreach ($params as $key => $param) {
            if (in_array($param['product_id'], $properties)) {
                $update[$key] = $this->filled($param);
            } else {
                $create[$key] = $this->filled($param);
            }
        }

        if ($update) {
            $res = $this->updateBatch($update, 'product_id', 'product_id');
        }
        if ($create) {
            $res = self::insert($create);
        }

        return $res;
    }

    protected function filled($params)
    {
        $data = [];

        foreach ($params as $key => $param) {
            if (is_bool($param)) { // bool类型转换为int类型
                $param = (int) $param;
            }
            if (is_array($param)) { // 数组编码
                $param = json_encode($param);
            }
            if (in_array($key, $this->fillable)) {
                $data[$key] = $param;
            }
        }

        return $data;
    }

    public function getPriceAttribute()
    {
        return number_format($this->attributes['price'] / 100, 2);
    }
}
