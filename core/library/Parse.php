<?php

namespace phpRedis\library;

class Parse
{
    use Single;

    public static function bindAddress($bindAddress)
    {
        $match = '/^(.*):\/\/([\w.]+)[:|\/|\?]?([0-9]+)?(.*)$/m';
        preg_match($match, $bindAddress, $res);
        if (!$res) {
            throw new \Exception("解析错误");
        }
        return array_combine(
            ['url', 'protocol', 'domain', 'port', 'uri'],
            $res
        );
    }

}