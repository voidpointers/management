<?php

namespace Product\Entities;

use App\Model;
use Product\Filters\ListingFilter;

class Listing extends Model
{
    use ListingFilter;

    protected $table = 'listings';

    protected $appends = ['state_str'];

    protected const STATE_STR = [
        'active' => '在线'
    ];

    protected $fillable = [
        'listing_id',
        'taxonomy_id',
        'shop_id',
        'user_id',
        'title',
        'price',
        'quantity',
        'image',
        'url',
        'views',
        'num_favorers',
        'state',
        'is_customizable',
        'should_auto_renew',
        'tags',
        'taxonomy_path',
        'description',
        'creation_tsz',
        'ending_tsz',
        'last_modified_tsz'
    ];

    public function images()
    {
        return $this->hasMany(Image::class, 'listing_id', 'listing_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'listing_id', 'listing_id');
    }

    public function store($params, $uk = 'listing_id')
    {
        $listing_ids = self::whereIn('listing_id', array_column($params, 'listing_id'))
        ->pluck('listing_id')
        ->all();

        $update = $create = [];
        foreach ($params as $key => $param) {
            if (in_array($param['listing_id'], $listing_ids)) {
                $update[] = $this->filled($param);
            } else {
                $create[$key] = $this->filled($param);
                $create[$key]['create_time'] = time();
            }
        }

        // 如果存在则更新
        if ($update) {
            $res = $this->updateBatch($update, 'listing_id', 'listing_id');
        }
        if ($create) {
            $res = self::insert($create);
        }
        return $res;
    }

    protected function filled($params)
    {
        $data = [
            'update_time' => time(),
            'shop_id' => shop_id()
        ];
        foreach ($params as $key => $param) {
            if ('Images' == $key) {
                $data['image'] = $param[0]['url_fullxfull'];
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
        }

        return $data;
    }

    public function getStateStrAttribute()
    {
        return self::STATE_STR[$this->attributes['state']] ?? '';
    }
}
