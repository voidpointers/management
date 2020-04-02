<?php

namespace Customer\Entities;

use App\Model;
use Customer\Filters\TemplateFilter;

class Template extends Model
{
    use TemplateFilter;

    protected $table = 'message_templates';
}
