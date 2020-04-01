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

    public function countGroup($params)
    {
        return $this->entities::select($params, DB::raw('COUNT(*) as total'))
        ->groupBy($params)
        ->get()
        ->keyBy($params);
    }

    public function countBy($params)
    {
        return $this->entities::where($params)
        ->count();
    }
}
