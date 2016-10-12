<?php

class Resource
{
    public function main(string $id='0')
    {
        if ((int)$id >0) {
            Upload::outputPublic((int)$id);
        } else {
            echo 'No Resource Id';
        }
    }
}
