<?php

use phpRedis\event\EventServer;

require_once "./vendor/autoload.php";

$port = 9808;
$context = stream_context_create(['socket' => ['backlog' => 2000]]);
stream_context_set_option($context, 'socket', 'so_reuseaddr', 1); //设置连接重用
$socket = stream_socket_server("tcp://0.0.0.0:{$port}", $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
stream_set_blocking($socket, false);//非阻塞
$objdump = EventServer::getSingle();
var_dump($objdump);
$objdump->add(Event::READ, $socket, function ($fd, $what, $args) {
    var_dump($fd, $what, $args);
    $client = stream_socket_accept($fd);
    var_dump($client);
    fputs($client, 'link ok');
    while (true) {
        $buf = fread($client, 4096);
        var_dump($buf);
    }
    var_dump($text);
});
$objdump->loop();