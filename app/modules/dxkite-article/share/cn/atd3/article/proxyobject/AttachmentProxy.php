<?php
namespace cn\atd3\article\proxyobject;


class AttachmentProxy extends ProxyObject
{
    public function getAttachment(int $article){
        return table('attachment')->list();
    }
}
