<?php

namespace Express\Contracts;

interface RequestInterface
{
    public function trackInfo($tracking_code = '');

    public function createOrder($params = []);
}
