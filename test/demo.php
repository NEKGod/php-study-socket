<?php

use phpRedis\event\EventServer;
use  phpRedis\Service;

require_once "../vendor/autoload.php";
$port = 9808;
$service = new Service("tcp://0.0.0.0:{$port}/index");
$service->start();