<?php

class Mail
{
    // 发送至
    public $to=[];
    // 来至
    public $from=[];
    // 邮件类型
    public $type='html';
    // 使用的邮件模板
    public $use='';
    // 直接发txt
    public $msg='';
    // 模板值
    public $values=[];

    public $subject='';

    // Mail To
    public function to(string $email, string $name='')
    {
        if ($name) {
            $this->to[$name]=$email;
        } else {
            $this->to[]=$email;
        }
        return $this;
    }
    public function subject(string $subject)
    {
        $this->subject=$subject;
        return $this;
    }
    public function from(string $email, string $name='')
    {
        $this->from=[$email,$name];
        return $this;
    }
    // raw message
    public function message(string $msg)
    {
        $this->msg=$msg;
        $this->type='txt';
        $this->use=null;
    }
    // 使用模板
    public function use(string $tpl)
    {
        $this->use=$tpl;
        return $this;
    }
    // 模板压入值
    public function assign(string $name, $value)
    {
        $this->values[$name]=$value;
        return $this;
    }
    
    // 发送邮件
    public function send(array $value_map=[])
    {
        // 合并属性值
        $this->values=array_merge($this->values, $value_map);
        $to=self::parseTo();
        $message=self::renderBody();
        $header=self::parseHeader();
        var_dump($to, $this->subject, $message, $header);
        var_dump(mail($to, $this->subject, $message, $header));
    }

    private function parseFrom()
    {
        if ($this->from[1]) {
            return "From: {$this->from[1]}<{$this->from[0]}>\r\n";
        } else {
            return 'From: '.$this->from[0] . "\r\n" ;
        }
    }

    private function parseHeader()
    {
        $header='MIME-Version: 1.0' . "\r\n";
        $header.='Content-Type:'.mime($this->type)."\r\n";
        $header.=self::parseFrom();
        $header.='To: '.self::parseTo()."\r\n";
        $header.='X-Mailer: XCore/'.CORE_VERSION."\r\n";
        return $header;
    }

    private function parseTo()
    {
        $to='';
        foreach ($this->to as $name => $email) {
            if (is_string($name)) {
                $to.="$name <$email>,";
            } else {
                $to.=$email.',';
            }
        }
        return rtrim($to, ',');
    }

    private function renderBody()
    {
        if ($this->use) {
            $file=View::viewPath('__mail__/'.$this->use);
            ob_start();
            $_Mail=new Core\Value($this->values);
            require $file;
            $this->message=ob_get_clean();
        }
    }
}
