<?php
namespace Home\Lib;
class Redis{
    private static $redisDb;
    static function getDb()
    {
        if(!self::$redisDb)
        {
            self::$redisDb = new \Redis();
            self::$redisDb->connect('127.0.0.1');
            return self::$redisDb;
        }
        return self::$redisDb;
    }
    
}