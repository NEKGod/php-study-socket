<?php

namespace phpRedis;

use Event;
use phpRedis\event\EventServer;
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
        $pid = $this->getProc()->startChildProc(1);
        $pid = $this->getProc()->startChildProc(2);
        $this->getProc()->setProcMain();
    }

    public function startProc()
    {

        die;
    }

    public function startServiceListenProc()
    {
        $service_socket = $this->listen($this->url_bind);
        $this->getEvent()->add(Event::READ, $service_socket, function ($fd, $what, $args) {
            var_dump($fd, $what, $args);
            fclose($fd);
//            $client = stream_socket_accept($fd);
//            var_dump($client);
//            fputs($client, 'link ok');
//            while (true) {
//                $buf = fread($client, 4096);
//                var_dump($buf);
//            }
        });
        $this->getEvent()->loop();
    }

    public function listen($url_bind)
    {
        $context = stream_context_create(['socket' => ['backlog' => 2000]]);
        stream_context_set_option($context, 'socket', 'so_reuseaddr', 1); //设置连接重用
        $this->service_socket = stream_socket_server($url_bind['url'], $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
        stream_set_blocking($this->service_socket, false);//非阻塞
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