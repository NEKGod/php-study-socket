<?php
pcntl_async_signals(TRUE);
$pids = [];
//信号处理函数
function sig_handler($signo, $data)
{
    global $pids;
    var_dump($signo);
    var_dump($data);
    switch ($signo) {
        case SIGTERM:
            echo "Terminating process\r\n";
            // 处理SIGTERM信号
            exit;
            break;
        case SIGHUP:
            //处理SIGHUP信号
            break;
        case SIGCLD:
            pcntl_waitpid($data['pid'], $status);
            unset($pids[0]);
            break;
        case SIGUSR1:
            echo "Caught SIGUSR1...\n";
            break;
        case SIGQUIT:
            echo "Caught SIGQUIT...\n";
            break;
        default:
            // 处理所有其他信号

    }

}

echo "Installing signal handler...\n";

//安装信号处理器
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGUSR1, "sig_handler");
pcntl_signal(SIGQUIT, "sig_handler");
pcntl_signal( SIGCHLD,  "sig_handler");

// 或者在PHP 4.3.0以上版本可以使用对象方法
// pcntl_signal(SIGUSR1, array($obj, "do_something");
echo "Generating signal SIGTERM to self...\n";
$pid = pcntl_fork();
if ($pid == 0) {
    pcntl_signal_dispatch();
    pcntl_signal( SIGCHLD,  "sig_handler");
    pcntl_signal_dispatch();
    var_dump("children ". posix_getpid());
    sleep(10);
}else {
    var_dump("parent process pid: " . posix_getpid());
    $pids[] = $pid;
    while (1) {
        if (empty($pids)) {
            break;
        }
        sleep(1);
    }
}
