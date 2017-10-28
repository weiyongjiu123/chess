<?php
namespace MyClass;
use Channel\Client;

class HttpGame{
    public static function forwarding($data)
    {
        Client::publish($data['to'],json_encode($data['content']),function (){});
        $data['connection']->send('success');
    }
}