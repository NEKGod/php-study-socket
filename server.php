#!/usr/bin/env php
<?php

use phpRedis\Service;

require_once "./vendor/autoload.php";

$port = 6666;
$service = new Service("tcp://0.0.0.0:{$port}/index");
$service->start();