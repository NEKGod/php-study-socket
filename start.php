<?php
$ip = "0.0.0.0";
$port = "8080";
$http_tcp_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($http_tcp_socket,SOL_SOCKET, SO_REUSEADDR, 1);
if (!socket_bind($http_tcp_socket, $ip, $port)) {
    exit("socket bind error :". socket_strerror(socket_last_error()));
}
if (!socket_listen($http_tcp_socket, 1024)) {
    exit("socket listen error:" . socket_strerror(socket_last_error()));
}

$http_body = "HTTP/1.1 200 OK
Host: 192.168.79.128:8080
Connection: close
Content-type: text/html; charset=UTF-8

你好世界
";
$i = 0;
while (true) {
    $i++;
    $client_socket = socket_accept($http_tcp_socket);
    if ($client_socket === false) {
        echo "<{$i}>:" . "socket accept error---".socket_strerror(socket_last_error());
        continue;
    }
    $accept_text = socket_read($client_socket, 1024 * 2);
    echo "<{$i}>:".$accept_text.PHP_EOL;
    echo str_repeat('-', 100).PHP_EOL;
    socket_write($client_socket, $http_body, strlen($http_body)); //, MSG_OOB | MSG_EOF | MSG_EOR);
    socket_shutdown($client_socket);
    socket_close($client_socket);
}
