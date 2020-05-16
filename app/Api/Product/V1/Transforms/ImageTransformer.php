<?php

namespace Api\Product\V1\Transforms;

use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;

class ImageTransformer extends TransformerAbstract
{
    public function transform($image)
    {
        if ($image) {
			$image->url_300 = transfer_image($image->url);
			$image->url_75 = transfer_image($image->url, 'il_fullxfull', 'il_75x75');
            return $image->attributesToArray();
        }
        return [];
    }
}
