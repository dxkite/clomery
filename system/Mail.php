<?php

final class Mail
{
    private static $mailer= null;
    public static function mailer()
    {
        if (is_null(self::$mailer)) {
            $mailer='mail\\'.conf('Mailer','Sendmail');
            self::$mailer=new $mailer;
        }
        return self::$mailer;
    }
}
