<?php

namespace Api\Fractal;

use League\Fractal\Serializer\ArraySerializer;

class DataArraySerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey) {
            return $resourceKey == 'include' ? $data : [$resourceKey => $data];
        }
        return ['data' => $data];
    }

    public function item($resourceKey, array $data)
    {
        if ($resourceKey) {
            return $resourceKey == 'include' ? $data : [$resourceKey => $data];
        }
        return ['data' => $data];
    }
}
