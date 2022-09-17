<?php

namespace phpRedis;

use Event;
use phpRedis\command\CommandFactory;
use phpRedis\event\EventServer;
use phpRedis\handle\SetHandle;
use phpRedis\library\Parse;
use phpRedis\library\Proc;

class Service
{
    private $url_bind = [];

    private $service_socket = null;
    /**
     * @var \phpRedis\event\EventServer
     */
    private $event;
    /**
     * @var \phpRedis\library\Proc
     */
    private $proc;


    /**
     * @throws \Exception
     */
    public function __construct($url_bind)
    {
        $this->url_bind = $this->parseUrl($url_bind);
        $this->setEvent(EventServer::getSingle());
        $this->setProc(Proc::getSingle());

    }

    public function start()
    {
        cli_set_process_title("php-redis");
        SetHandle::getSingle();
        print('当前id:' . $this->getProc()->getProcId() . "\r\n");
        $this->forkMasterProc();
        if ($this->getProc()->getProcId() == 'service') {
            $this->getProc()->recycling();
        }

    }

    private function forkWorkProc($args): void
    {
        $this->getProc()->startChildProc('work', [$this, 'clientLink'], $args);
    }

    private function forkMasterProc(): void
    {
        $this->getProc()->startChildProc('master', [$this, 'startServiceListenProc']);
    }


    public function clientLink($args)
    {
        $client = $args['fd'];
        while (true) {
            $buf = fread($client, 2048);
            $buf = trim($buf);
//            echo "client " . (int) $client . "\t" . 'msg -> ' . $buf . PHP_EOL;
            $command = CommandFactory::execCommand($buf);
            fputs($client, $command, strlen($command));
            if (!$buf || $buf == 'exit') {
                fclose($client);
                exit(0);
            }
        }
    }

    public function startServiceListenProc()
    {
        $service_socket = $this->listen($this->url_bind);
        while (true) {
            $client = @stream_socket_accept($service_socket);
            if (!$client) {
                continue;
            }
            echo "client connected to service". (int) $client.PHP_EOL;
            $this->forkWorkProc([
                'fd' => $client,
            ]);
        }
    }

    public function listen($url_bind)
    {
        $context = stream_context_create(['socket' => ['backlog' => 2000]]);
        stream_context_set_option($context, 'socket', 'so_reuseaddr', 1); //设置连接重用
        $this->service_socket = stream_socket_server($url_bind['url'], $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
        stream_set_blocking($this->service_socket, true);//非阻塞
        var_dump($this->service_socket);
        return $this->service_socket;
    }

    public function parseUrl($url)
    {
        $data = parse_url($url);
        return array_merge($data, ['url' => $url,]);
    }

    /**
     * @return \phpRedis\event\EventServer
     */
    public function getEvent(): EventServer
    {
        return $this->event;
    }

    /**
     * @param \phpRedis\event\EventServer $event
     */
    public function setEvent(EventServer $event): void
    {
        $this->event = $event;
    }

    /**
     * @return \phpRedis\library\Proc
     */
    public function getProc(): Proc
    {
        return $this->proc;
    }

    /**
     * @param \phpRedis\library\Proc $proc
     */
    public function setProc(Proc $proc): void
    {
        $this->proc = $proc;
    }



}