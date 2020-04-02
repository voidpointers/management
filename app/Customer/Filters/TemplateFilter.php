<?php

namespace Customer\Filters;

use App\QueryFilter;

trait TemplateFilter
{
    use QueryFilter;

    public function title($params)
    {
        return $this->builder->where('title', 'like', "$params%");
    }
}
