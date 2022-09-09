<?php

namespace phpRedis;

use phpRedis\library\Parse;

class Service
{

    public function __construct($bindAddress)
    {
        $address = Parse::bindAddress($bindAddress);

    }
}