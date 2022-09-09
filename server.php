#!/usr/bin/env php
<?php
require_once "./vendor/autoload.php";

$port = 9808;
function eventSighandler()
{

}

$base = new EventBase();
$ev = Event::signal($base, SIGTERM, 'eventSighandler', null);
$ev->add();
$context = stream_context_create(['socket' => ['backlog' => 2000]]);
stream_context_set_option($context, 'socket', 'so_reuseaddr', 1); //设置连接重用
$socket = stream_socket_server("tcp://0.0.0.0:{$port}", $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
stream_set_blocking($socket, false);//非阻塞

//监听服务端的socket
$event = new Event($base, $socket, Event::PERSIST | Event::READ | Event::WRITE, function ($fd, $what, $arg) {
    $pid = pcntl_fork();
    $client = stream_socket_accept($fd);
    fputs($client, 'link ok');
    //绑定事件
    $base = new EventBase();
    var_dump('发生新的连接：',$client);
    $event = null;
    //监听客户端socket
    $event = new Event($base, $client,  Event::PERSIST | Event::READ | Event::WRITE, function ($client) use(&$event) {
        $buf = fread($client, 2048);
        $buf = trim($buf);
        var_dump((int) $client,$buf);
        if ($buf) {
            fputs($client, "ok", strlen("ok"));
        }
        if (!$buf || $buf == 'exit') {
            fclose($client);
            $event->del();
        }
    });
    $event->add(); //加入事件监听
    $base->loop();
}, null);
$event->add();
$base->loop();