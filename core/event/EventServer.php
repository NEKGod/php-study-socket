<?php

namespace phpRedis\event;

use Event;
use EventBase;
use phpRedis\library\Single;

class EventServer
{
    use Single;

    private $eventBase = null;
    private $taskMap = [];

    private function __construct(){
        $this->eventBase = new EventBase();
    }

    public function add($flags, $fd, $callable, $arg = null): bool
    {
        var_dump($flags);
        $event = new Event($this->eventBase, $fd, $flags, $callable, $arg);
        $event->add();
        $this->taskMap[(int) $fd] = $event;
        return true;
    }

    public function loop()
    {
        $this->eventBase->loop();
    }
}