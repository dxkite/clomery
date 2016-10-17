<?php

class WebSocket
{
    // 用户链接
    public static $clients=[];
    public static $port=9505;
    public static $host='127.0.0.1';
    public static $socket;
    public static $version='1.x-dev';
    
    public static function listen(int $port=0)
    {
        //创建tcp socket
        self::$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option(self::$socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind(self::$socket, 0, self::$port);
        //监听端口
        socket_listen(self::$socket);
        //连接的client socket 列表
        self::$clients[]=self::$socket;
        $null=null;
        //设置一个死循环,用来监听连接 ,状态
        while (true) {
            $changed = self::$clients;
            socket_select($changed, $null, $null, 0, 10);
    
            //如果有新的连接
            if (in_array(self::$socket, $changed)) {
                //接受并加入新的socket连接
                $socket_new = socket_accept(self::$socket);
                self::$clients[] = $socket_new;
                
                //通过socket获取数据执行handshake
                $header = socket_read($socket_new, 1024);
                self::perform_handshaking($header, $socket_new, self::$host, $port);
                //获取client ip 编码json数据,并发送通知
                socket_getpeername($socket_new, $ip);
                self::pushMessage(json_encode(array('type'=>'system', 'message'=>$ip.' connected')));
                $found_socket = array_search(self::$socket, $changed);
                unset($changed[$found_socket]);
            }
            
            //轮询 每个client socket 连接
            foreach ($changed as $changed_socket) {
                
                //如果有client数据发送过来
                while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
                    //解码发送过来的数据
                    $received_text = self::unmaskBody($buf);
                    $tst_msg = json_decode($received_text);
                    $user_name = $tst_msg->name;
                    $user_message = $tst_msg->message;
                    self::pushMessage(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message)));
                    break 2;
                }
                
                //检查offline的client
                $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
                if ($buf === false) {
                    $found_socket = array_search($changed_socket, self::$clients);
                    socket_getpeername($changed_socket, $ip);
                    unset(self::$clients[$found_socket]);
                    self::pushMessage(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
                }
            }
        }
    }
    // 监听用户推送的消息
    public static function pullMessage($callback)
    {
    }
    // 发送消息到用户
    public static function pushMessage(string $message, int $client_id=-1)
    {
        $msg=self::maskBody($message);
        if ($client_id<0) {
            foreach (self::$clients as $client) {
                @socket_write($client, $msg, strlen($msg));
            }
        } else {
            socket_write($clients[$client_id], $msg, strlen($msg));
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

    public static function close()
    {
        // 关闭监听的socket
        socket_close(self::$socket);
    }
    //握手的逻辑
public static function perform_handshaking($receved_header, $client_conn, $host, $port)
    {
        $headers = array();
        $lines = preg_split("/\r\n/", $receved_header);
        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }

        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
    "Upgrade: websocket\r\n" .
    "Connection: Upgrade\r\n" .
    "WebSocket-Origin: $host\r\n" .
    "WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
    "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        socket_write($client_conn, $upgrade, strlen($upgrade));
    }
}
