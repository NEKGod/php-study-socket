<?php

namespace phpRedis\command;

class CommandFactory
{
    const classMap = [
        'set' => Set::class,
        'get' => Get::class,
    ];

    const COMMAND = 0;

    public static function execCommand($command){
        $command = explode(' ', $command);
        if (empty(self::classMap[$command[self::COMMAND]])) {
            throw new \Exception("命令不存在");
        }
        return call_user_func([new (self::classMap[$command[self::COMMAND]])(), 'baseExecute'], array_slice($command, 1));
    }

}