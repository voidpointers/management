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
        dd($params);
        $params = array_map(function ($value) {
            return [];
        }, $params);
        $properties = self::whereIn('product_id', array_column($params, 'product_id'))
            ->pluck('product_id')
            ->all();

        $update = $create = [];

        foreach ($params as $param) {
            foreach ($params as $key => $param) {
                if (in_array($param['product_id'], $properties)) {
                    $update[] = $this->filled($param);
                    $update[$key]['price'] = $listing_id ? ($param['price'] ?? 0) : $param['price'] * 100;
                    $update[$key]['quantity'] = $param['quantity'] ?? 0;
                } else {
                    $create[$key] = $this->filled($param);
                    $create[$key]['listing_id'] = $listing_id ?? $param['listing_id'];
                    $create[$key]['price'] = $listing_id ? ($param['price'] ?? 0) : $param['price'] * 100;
                    $create[$key]['quantity'] = $param['quantity'] ?? 0;
                }
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
        foreach ($params as $key => $param) {
            if ($key == 'property_values') {
                foreach ($param as $item) {
                    $data['properties'][] = ['property_name' => $item['property_name'], 'scale_id' => $item['scale_id'], 'scale_name' => $item['scale_name'], 'values' => $item['values'][0]];
                }
                $data['properties'] = isset($data['properties']) ? json_encode($data['properties']) : json_encode(null);
                continue;
            } elseif ($key == 'offerings') {
                $data['price'] = $param[0]['price']['amount'];
                $data['quantity'] = $param[0]['quantity'];
                $data['is_enabled'] = $param[0]['is_enabled'] ?? 0;
                continue;
            }

            if (is_bool($param)) { // bool类型转换为int类型
                $param = (int) $param;
            }
            if (is_array($param)) { // 数组编码
                $param = json_encode($param);
            }
            if (in_array($key, $this->fillable)) {
                $data[$key] = $param;
            }
            if ($key == 'properties') {
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
