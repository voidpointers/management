<?php

namespace Api\Product\V1\Transforms;

use League\Fractal\TransformerAbstract;

class InventorTransformer extends TransformerAbstract
{
    public function transform($inventory)
    {
        if ($inventory) {

			$properties = json_decode ($inventory->properties, true);
			$inventory->property_name1 = isset($properties[0]) ?  $properties[0]['property_name'] : "";
			$inventory->property_value1 =  isset($properties[0]) ?  $properties[0]['values'] : "";
			$inventory->property_name2 = isset($properties[1]) ?  $properties[1]['property_name'] : "";
			$inventory->property_value2 = isset($properties[1]) ?  $properties[1]['values'] : "";

            return $inventory->attributesToArray();
        }
        return [];
    }
}
