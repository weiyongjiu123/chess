<?php

namespace AppClass;
use Channel\Client;
use \GatewayWorker\Lib\Gateway;

include_once 'ChannelClient.php';

class Index{

    function init()
    {
        \Channel\Client::connect('127.0.0.1', 2206);
        \Channel\Client::on('webSocket',function ($data){
            $data = json_decode($data,true);
            $method = $data['type'];
            self::$method($data['content']);
        });
    }
    //转发
    static function forwarding($data)
    {
        print_r($data);
    }
   /*
    * $data={ roomId:$roomId ,client_id:$client_id,isPlayer:3} / { roomId:1,client_id:2,looker:3,isPlayer:4}
    * 浏览器发来的加入房间的通知
    */
   static function joinRoom($data)
   {
       Gateway::joinGroup($data['client_id'],'room_'.$data['roomId']);      //加入组，方便群发
      $redisDb = Redis::getRedis();
       $redisDb->hIncrBy('room_'.$data['roomId'],'onlineLooker',1);     //添加观看人数
       if($data['isPlayer'])                                //判断是否是参赛者
       {
           $socketToken = $redisDb->get($data['player']);
           if($socketToken == $data['socketToken'])             //验证参赛者是否合法
           {
               $redisDb->set($data['player'].'_socketId',$data['client_id']);
               Gateway::setSession($data['client_id'],[         //保存参赛者的session
                   'roomId'=>$data['roomId'],
                   'player'=>$data['looker']
               ]);
               Gateway::sendToGroup('room_'.$data['roomId'],json_encode([
                   'type'=>'playerComeIn',
                   'content'=>[
                       'player'=> $data['looker']
                   ]
               ]));
               Client::publish('room'.$data['roomId'],json_encode([     //设置参赛者是否已经进入房间
                   'type'=>'setPlayer',
                   'content'=>[
                       'player'=>$data['looker']
                   ]
               ]),function (){});
           }
       }else{
           Gateway::setSession($data['client_id'],[                     //(非参赛者)保存用户所在的房间，方便以后离开时更新房间观看的人数
               'roomId'=>$data['roomId'],
               'player'=>0
           ]);
       }

   }
    //浏览器客户端发给chess进程
    static function toChess($data)
    {
//        print_r($data);
        \Channel\Client::publish($data['toChess'],json_encode($data['content']),function (){});
    }
    //chess进程发给浏览器客户端
    static function toClient($data){
//        print_r($data);
        Gateway::sendToGroup($data['toGroup'],json_encode($data['content']));
    }
    //浏览器发消息给time进程，通知time开始比赛计时
    static function toTime($data)
    {
        Client::publish($data['toTime'],json_encode($data['content']),function (){});
    }

    /**
     * description 用户离开房间时，更新房间观看的人数
     * var $_SESSION = [ roomId:1,player:2 ]
     */
    static function decOnlineLooker()
    {
        $roomId = $_SESSION['roomId'];
        $redisDb = Redis::getRedis();
        $redisDb->hIncrBy('room_'.$roomId,'onlineLooker',-1);
        if($_SESSION['player'])
        {
            Client::publish('room'.$roomId,json_encode([
                'type'=>'setPlayerLeave',
                'content'=>[
                    'player'=>$_SESSION['player']
                ]
            ]),function (){});
            Gateway::sendToGroup('room_'.$roomId,json_encode([
                'type'=>'playerLeave',
                'content'=>[
                    'player'=>$_SESSION['player']
                ]
            ]));
        }
    }
    //根据指定的client_id单发送
    static function toClientOne($data){
        Gateway::sendToClient($data['client_id'],json_encode($data['content']));
    }
    
    
}