<?php
namespace MyClass;
use Channel\Client;
require_once __DIR__.'/../Other/ChannelClient.php';
require_once __DIR__.'/../MyClass/HttpGame.php';

class HttpIndex{
    private static $roomArr = [
        'room1'=>false,
        'room2'=>false
    ];
    public static function init()
    {
        Client::connect('127.0.0.1',2206);
    }
    public static function msg($data)
    {
        $method = $data['type'];
        self::$method($data);
    }
    public static function forwarding($data)
    {
        Client::publish($data['to'],json_encode($data['content']),function (){});
        $data['connection']->send('success');
    }
    
}