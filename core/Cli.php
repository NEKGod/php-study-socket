<?php

namespace phpRedis;

class Cli
{
    private $serverFd = null;

    public function start()
    {
        $this->connectServer();
        exec("reset");
        $stdout = fopen('php://stdout', 'w+b');
        while (true) {
            $input = $this->inputCommand();
            fputs($this->getServerFd(), $input);
            $res = fread($this->getServerFd(), 2048);
            var_dump($res);
        }
    }

    public function inputCommand()
    {
        printf("command > ");
        return fgets(STDIN);
    }

    public function connectServer()
    {
        $fp = stream_socket_client("tcp://127.0.0.1:6666", $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
            die;
        }
        $this->setServerFd($fp);
    }

    /**
     * @return resource|null
     */
    public function getServerFd()
    {
        return $this->serverFd;
    }

    /**
     * @param resource $serverFd
     */
    public function setServerFd($serverFd): void
    {
        $this->serverFd = $serverFd;
    }
}