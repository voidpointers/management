<?php

namespace Logistics\Repositories;

use App\Repository;
use Logistics\Entities\Channel;

class ChannelRepository extends Repository
{
    public function model()
    {
        return Channel::class;
    }
}
