<?php

namespace App;

use Illuminate\Http\Request;

trait QueryFilter
{
    protected $builder;

    protected $request;

    public function apply(Request $request)
    {
        $this->builder = static::query();

        foreach ($request->all() as $name => $value) {
            $name = camelize($name, '_');
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value], function ($item) {
                    if ('' === $item || null === $item) {
                        return false;
                    }
                    return true;
                }));
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }
}
