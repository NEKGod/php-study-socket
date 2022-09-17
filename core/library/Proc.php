<?php

namespace phpRedis\library;

class Proc
{
    use Single;

    private $procId = "service";

    private static $childrenProcPidMap = [];

    public function start($index)
    {

    }

    public function isChildrenProcPidMap($pid): ?int
    {
        return static::$childrenProcPidMap[$pid] ?? null;
    }

    public function setChildrenProcPidMap($pid): void
    {
        static::$childrenProcPidMap[$pid] = $pid;
    }

    public function startChildProc(string $index, $callable = null, $args = []): int
    {
        $pid = pcntl_fork();
        if ($pid == 0) {
            cli_set_process_title("php-redis/{$index}");
            $this->setProcId($index);
            var_dump($index."号子进程:". posix_getpid());
            if ($callable) {
                call_user_func($callable, $args);
            }
            sleep(mt_rand(1,3));
        }else{
            $this->setChildrenProcPidMap($pid);
            $this->signal();
        }
        return $pid;
    }

    public function recycling()
    {
        while (1) {
            // 主进程 只负责回收子进程
            if (empty(static::$childrenProcPidMap)) {
                break;
            }
            sleep(1);
        }
    }

    /**
     * @return null
     */
    public function getProcId(): ?string
    {
        return $this->procId;
    }

    /**
     * @param null $procId
     */
    public function setProcId($procId): void
    {
        $this->procId = $procId;
    }

    public function signal()
    {
        pcntl_async_signals(true);
        //安装信号处理器
        pcntl_signal(SIGTERM,    [$this,"sig_handler"], false);
        pcntl_signal( SIGCHLD,   [$this,"sig_handler"], false);
    }

    public function sig_handler($signo, $data)
    {
        pcntl_waitpid(-1, $status, WNOHANG);
        unset(static::$childrenProcPidMap[$data['pid']]);
//        switch ($signo) {
//            case SIGCHLD:
//                pcntl_waitpid($data['pid'], $status);
//                unset(static::$childrenProcPidMap[$data['pid']]);
//                break;
//        }
//        var_dump($status);
//        var_dump(static::$childrenProcPidMap);
    }

    private function getProc(): Proc
    {
        return $this;
    }
}