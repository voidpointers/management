<?php

namespace Aggregate\Services;

use Illuminate\Support\Facades\DB;

class AggregateFactory
{
    protected $entities;

    public function setEntities($entities)
    {
        $this->entities = new $entities;
        return $this;
    }

    public function count(array $params = [])
    {
        return $this->entities::select('shop_id', DB::raw('COUNT(*) as total'))
        ->groupBy('shop_id')
        ->get()
        ->keyBy('shop_id');
    }
}
