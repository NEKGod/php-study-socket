<?php

namespace phpRedis\handle;

use phpRedis\library\Single;

class SetHandle
{
    use Single;

    private $data = [];

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function get($key){
        return $this->data[$key] ?? null;
    }
}