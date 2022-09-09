<?php
$ip = '127.0.0.1';
$port = 9011;
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (!socket_connect($socket, $ip, $port)){
    echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
    die;
}
//$msg = "你好世界".date('Y-m-d H:i:s') . '#' . mt_rand(1, 10000);
//$msg = "hello world". PHP_EOL;
while (true) {
    echo "请输入提交内容:";
    $msg = fgets(STDIN);
    socket_send($socket, $msg, strlen($msg), MSG_EOR | MSG_EOF);
    do{
        $buf = socket_read($socket, 2048);
        if ($buf){
            var_dump($buf);
        }
        if ($buf == 'ok') {
//            socket_send($socket, 'exit', strlen('exit'), MSG_EOR | MSG_EOF);
            break;
        }
    }while($buf !== false);
}

socket_shutdown($socket);
socket_close($socket);