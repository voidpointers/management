<?php

namespace Aggregate\Contracts;

interface AggregateInterface
{
    public function count(array $params = []);
}
