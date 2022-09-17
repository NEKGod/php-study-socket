<?php
$ip = '127.0.0.1';
$port = 9808;
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (!socket_connect($socket, $ip, $port)){
    echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
    die;
}
class LinkEvent {
    const LINK_EVENT = '1';
    const MSG_SUCCESS_EVENT = '2';
}

function msgReadListener($socket, $type = '')
{
    do{
        $buf = socket_read($socket, 2048);
        $buf = trim($buf);
        if ($buf){
            var_dump($buf);
        }
        if ($type == LinkEvent::LINK_EVENT){
            if ($buf == 'link ok') {
                break;
            }
            var_dump('wait event link' . PHP_EOL);
            usleep(10000);
            continue;
        }
        if ($buf == 'ok') {
//            socket_send($socket, 'exit', strlen('exit'), MSG_EOR | MSG_EOF);
            break;
        }
        if ($buf == 'exit') {
            exit();
        }
    }while($buf !== false);
}

msgReadListener($socket, LinkEvent::LINK_EVENT);
while (true) {
    echo "请输入提交内容:";
    $msg = fgets(STDIN);
    socket_send($socket, $msg, strlen($msg), MSG_EOR | MSG_EOF);
    msgReadListener($socket, LinkEvent::MSG_SUCCESS_EVENT);
}
//socket_shutdown($socket);
//socket_close($socket);