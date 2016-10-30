<?php

class WebSocket_Server
{
    // 用户链接
    public  $clients=[];
    public  $port=2015;
    public  $host=0;
    public  $socket;
    public  $version='1.x-dev';
    public  $callers=[];
    public  $errno=0;
    public  $error=[
        2=>'unable to write to socket',
    ];

    public  function listen(int $port=0)
    {
        //创建tcp socket
        $this->socket = socket_create(AF_INET,SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR,true);
        socket_bind($this->socket, 0 , $this->port);
        //监听端口
        socket_listen($this->socket);
       
        socket_getsockname($this->socket, $ip,$port);
        printf("Server Open %s:%d\n",$ip,$port);
        //连接的client socket 列表
        $this->clients[]=$this->socket;
        $null=null;
        //设置一个死循环,用来监听连接 ,状态
        while (true) {
            $readable = $this->clients;
           
            socket_select($readable, $null, $null, 0, 10);
            
            //如果有新的连接
            if (in_array($this->socket, $readable)) {
               
                //接受并加入新的socket连接
                $socket_new = socket_accept($this->socket);
                $this->clients[] = $socket_new;
                
                //通过socket获取数据执行handshake
                $header = socket_read($socket_new, 1024);
                self::perform_handshaking($header, $socket_new, $this->host, $port);
                //获取client ip 编码json数据,并发送通知
                socket_getpeername($socket_new, $ip);
                printf("Client %s Connected\n",$ip);
                self::pushMessage(json_encode(array('type'=>'system', 'message'=>System::user()->name.'('.$ip.') connected')));
                $found_socket = array_search($this->socket, $readable);
                unset($readable[$found_socket]);
            }
            
            //轮询 每个client socket 连接
            foreach ($readable as $id => $client_readable) {
                
                //如果有client数据发送过来
                while (socket_recv($client_readable, $buf, 1024, 0) >= 1) {
                    //解码发送过来的数据
                    self::pullMessage($id, self::unmaskBody($buf));
                    break 2;
                }
                
                //检查offline的client
                $buf = @socket_read($client_readable, 1024, PHP_NORMAL_READ);
                if ($buf === false) {
                    $found_socket = array_search($client_readable, $this->clients);
                    socket_getpeername($client_readable, $ip);
                    unset($this->clients[$found_socket]);
                    self::pushMessage(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
                }
            }
        }
    }
    public  function pullMessage(int $id, string $message)
    {
        foreach ($this->callers as $caller) {
            $caller->args($id, $message);
        }
    }
    // 监听用户推送的消息
    public function onMessage($callback)
    {
        $this->callers[]=new Core\Caller($callback);
    }
    // 发送消息到用户
    public  function pushMessage(string $message, int $client_id=-1)
    {
        $msg=self::maskBody($message);
        if ($client_id<0) {
            foreach ($this->clients as $key=> $client) {
                echo ('pushMessage To '. $key.':'.$message."\n");
                @socket_write($client, $msg, strlen($msg));
            }
        } else {
            @socket_write($clients[$client_id], $msg, strlen($msg));
        }
    }

    // 解码信息
    public static function unmaskBody(string $body):string
    {
        $length = ord($body[1]) & 127;
        if ($length == 126) {
            $masks = substr($body, 4, 4);
            $data = substr($body, 8);
        } elseif ($length == 127) {
            $masks = substr($body, 10, 4);
            $data = substr($body, 14);
        } else {
            $masks = substr($body, 2, 4);
            $data = substr($body, 6);
        }
        $body = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $body .= $data[$i] ^ $masks[$i%4];
        }
        return $body;
    }
    // 编码信息
    public static function maskBody(string $body):string
    {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($body);
        
        if ($length <= 125) {
            $header = pack('CC', $b1, $length);
        } elseif ($length > 125 && $length < 65536) {
            $header = pack('CCn', $b1, 126, $length);
        } elseif ($length >= 65536) {
            $header = pack('CCNN', $b1, 127, $length);
        }
        return $header.$body;
    }

    public  function close()
    {
        // 关闭监听的socket
        socket_close($this->socket);
    }
    //握手的逻辑
    public function perform_handshaking($receved_header, $client_conn, $host, $port)
    {
        $headers = array();
        $lines = preg_split("/\r\n/", $receved_header);
        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        if (isset($headers['Cookie']))
        {
            Cookie::parseFromString($headers['Cookie']);
        }
        
        var_dump(System::user()->hasSignin);
        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
                    "Upgrade: websocket\r\n" .
                    "Connection: Upgrade\r\n" .
                    "WebSocket-Origin: $host\r\n" .
                    "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        socket_write($client_conn, $upgrade, strlen($upgrade));
    }
    public static function write($socket,string $string,int $length){
        set_error_handler(['WebSocket','errorHander']);
        $return=socket_write($socket,$string, $length);
        restore_error_handler();
        return $return;
    }

    public function errorHander(int $errno, string $errstr, string $errfile, int $errline, array $errcontext)
    {
        // TODO ....
    }
}
