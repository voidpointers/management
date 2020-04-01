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

    public function countGroup($params, $where = [])
    {
        $query = $this->entities::select($params, DB::raw('COUNT(*) as total'));
        if ($where) {
            $query = $query->where($where);
        }

        return $query->groupBy($params)
        ->get()
        ->keyBy($params);
    }

    public function countBy($params)
    {
        return $this->entities::where($params)
        ->count();
    }
}
