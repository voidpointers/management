<?php

namespace Api\Customer\V1\Transforms;

use Customer\Entities\Template;
use League\Fractal\TransformerAbstract;

class TemplateTransformer extends TransformerAbstract
{
    public function transform(Template $template)
    {
        return $template->attributesToArray();
    }
}
