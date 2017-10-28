<?php
namespace AppClass;
class Redis{
    private static $redis;
    public static function getRedis()
    {
        if(!self::$redis)
        {
            self::$redis = new \Redis();
            self::$redis->connect('127.0.0.1');
        }
        return self::$redis;
    }
}