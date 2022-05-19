<?php
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, '127.0.0.1', 8080);

$send_body = "GET / HTTP/1.1
Host: 192.168.79.128:8080
Connection: keep-alive";
while (true){
$res = socket_send($socket, $send_body, strlen($send_body), MSG_EOR | MSG_OOB);
if (!$res) {
    die("socket_send erorr". socket_strerror(socket_last_error()));
}
}
socket_close($socket);
