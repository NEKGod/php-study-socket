<?php
$ip = '0.0.0.0';
$port = 9011;
$socket = socket_create(
    AF_INET,
    SOCK_STREAM,
    SOL_TCP
);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_option($socket, SOL_SOCKET, SO_REUSEPORT, 1);
if (!socket_bind($socket, $ip, $port)) {
    die(socket_last_error());
}
socket_listen($socket, 1024);
while (true) {
    $client = socket_accept($socket);
    var_dump("有连接连接上来了", $client);
    $msg = "";
    do{
        $buf = socket_read($client, 2048);
        $msg .= $buf;
        if ($buf) {
            var_dump($buf);
            socket_send($client, 'ok', strlen('ok'), MSG_EOF | MSG_EOR);
        }
        if ($buf == 'exit') {
            break;
        }
    }while($buf != false);
    var_dump($msg);
    socket_shutdown($client);
    socket_close($client);
}