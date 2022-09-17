<?php

namespace phpRedis\command;

interface CommandInterface
{
    public function __construct();
    public function execute(array $args = []);
}