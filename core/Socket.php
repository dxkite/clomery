<?php
class TCPSocket
{
    public $socket;
    public $host;
    public function __construct(string $host, int $port)
    {
        self::listen($host, $port);
    }

    public function listen(string $host, int $port)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_bind($this->socket, $host, $port);
        socket_listen(self::$socket);
    }
    public function accept()
    {
    }
    public function errorHander(int $errno, string $errstr, string $errfile, int $errline, array $errcontext)
    {
    }
}
