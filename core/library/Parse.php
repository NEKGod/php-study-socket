<?php

namespace phpRedis\library;

class Parse
{
    use Single;

    public static function bindAddress($bindAddress)
    {
        $match = '/^(.*):\/\/(.*)\/(.*)/m';
        preg_match_all($match, $bindAddress, $res);
        array_combine(
            ['head', 'domain', 'prot'],
            []
        );
    }

}