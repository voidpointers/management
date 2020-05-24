<?php

namespace Product\Entities;

use App\Model;
use Illuminate\Support\Arr;

class Image extends Model
{
    protected $table = 'listing_images';

    /**
     * 创建时间
     */
    const CREATED_AT = null;

    /**
     * 更新时间
     */
    const UPDATED_AT = null;

    protected $fillable = ['listing_id', 'url', 'image_id', 'sort'];

    public function store($params, $uk = 'listing_id')
    {
        $listings = self::whereIn('listing_id', array_column($params, 'listing_id'))
        ->get();

        $create = $update = [];

        foreach ($params as $param) {
            $groups = $listings->where('listing_id', $param['listing_id']);
            foreach ($param['Images'] as $image) {

                // 判断当前位置是否存在图片
                $sorts = $groups->pluck('image_id', 'sort')->all();

                if (($cur = $sorts[$image['rank']] ?? false)) {
                    $update[] = $this->filled($image);
                } else {
                    $image['is_sync'] = 1;
                    $create[] = $this->filled($image);
                }
            }
        }

        if ($create) {
            self::insert($create);
        } 
        if ($update) {
            self::updateBatch($update, 'image_id', 'image_id');
        }
        return true;
    }

	public function storeV2($params, $uk = 'listing_id'){
		$create = $update = [];
		self::whereIn('listing_id', array_column($params, 'listing_id'))->delete();
		foreach ($params as $param) {
			foreach ($param['Images'] as $image) {
				$image['is_sync'] = 1;
				$create[] = $this->filled($image);
			}
		}

		if ($create) {
			self::insert($create);
		}

		return true;
	}


    public function saveById($params)
    {
        $create = $update = [];

        foreach ($params as $param) {
            if ($param['id']) {
                $update[] = $param;
            } else {
                $create[] = $param;
            }
        }

        if ($create) {
            self::insert($create);
        } 
        if ($update) {
            self::updateBatch($update);
        }
        return true;
    }

    protected function filled($params)
    {
        $data = [
            'listing_id' => $params['listing_id'],
            'image_id' => $params['listing_image_id'],
            'url' => $params['url_fullxfull'],
            'sort' => $params['rank'],
            'is_sync' => $params['is_sync'] ?? 0,
        ];
        if (Arr::has($params, 'id')) {
            $data['id'] = $params['id'];
        }
        return $data;
    }

    public function saveBySort($params)
    {
        $create = $update = [];

        $images = self::whereIn('listing_id', array_column($params, 'listing_id'))
        ->get();

        foreach ($params as $key => $param) {
            $image = $images->where('listing_id', $param['listing_id']);
            if ($image) {
                if ($sort = $image->where('sort', $param['sort'])->first()) {
                    $update[$key] = $param;
                    $update[$key]['id'] = $sort->id;
                } else {
                    $create[] = $param;
                }
            } else {
                $create[] = $param;
            }
        }

        if ($create) {
            self::insert($create);
        } 
        if ($update) {
            self::updateBatch($update);
        }
        return true;
    }

    public function saveByFull(array $params)
    {
        $data = $first = [];

        foreach ($params as $param) {
            $data[] = [
                'listing_id' => $param['listing_id'],
                'url' => $param['url'],
                'sort' => $param['sort'],
//                'image_id' => $param['image_id']
            ];
            if (1 == ($param['sort'] ?? 1)) {
                $first[] = [
                    'listing_id' => $param['listing_id'],
                    'url' => $param['url'],
                ];
            }
        }

        // 删除原来所有图片
        self::whereIn('listing_id', array_column($params, 'listing_id'))
        ->delete();

        self::insert($data);

        return $first;
    }
}
