<?php

namespace phpRedis\library;

class Proc
{
    use Single;

    private $childrenProcPidMap = [];

    public function start($index)
    {

    }

    public function isChildrenProcPidMap($pid): ?int
    {
        return $this->childrenProcPidMap[$pid] ?? null;
    }

    public function setChildrenProcPidMap($pid): void
    {
        $this->childrenProcPidMap[$pid] = $pid;
    }

    public function startChildProc(int $index)
    {
        $pid = pcntl_fork();
        if ($pid == 0) {
            $this->setChildrenProcPidMap(posix_getpid());
            var_dump($index."号~子进程". posix_getpid());
        }
        return $pid;
    }

    public function setProcMain()
    {
        // 主进程 只负责回收子进程
        foreach ($this->childrenProcPidMap as $pid) {
            pcntl_waitpid($pid, $status);
        }
    }
}