<?php
namespace MyClass;
class Redis{
    static private $redisDb = null;
    static function getRedisDb()
    {
        if(!self::$redisDb)
        {
            self::$redisDb = new \Redis();
            self::$redisDb->connect('127.0.0.1');
        }
        return self::$redisDb;
    }
}