<?php

namespace Product\Entities;

use App\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'category_id',
        'name',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'page_description',
        'page_title',
        'category_name',
        'short_name',
        'long_name',
        'num_children',
        'parent_id'
    ];

    public function store($params, $parent_id = 0)
    {
        $data = [];

        foreach ($params as $key => $param) {
            foreach ($this->fillable as $fillable) {
                $data[$key][$fillable] = $param[$fillable] ?? '';
                if ($fillable == 'parent_id') {
                    $data[$key][$fillable] = $parent_id;
                }
            }
        }
        Category::insert($data);

        return true;
    }
}
