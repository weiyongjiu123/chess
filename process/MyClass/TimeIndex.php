<?php
namespace Myclass;
use Channel\Client;
use Workerman\Lib\Timer;

class TimeIndex{
    static private $roomArr = [];
    static private $countdownArr = [

    ];
    static private $timeIdArr = [];
    static function init()
    {
        Client::connect('127.0.0.1',2206);
        Client::on('time_1',function ($data){
            $data = json_decode($data,true);
            $method = $data['type'];
            self::$method($data['content']);
        });
    }

    /*
     * description 游戏开始前的倒计时
     * $data = { roomId:1,who: 2  }
     */
    static function beforePlay($data){
        $roomId = $data['roomId'];
        self::$roomArr[$roomId] = true;     //标志棋赛已经开始
        self::$countdownArr[$roomId][1] = 600;
        self::$countdownArr[$roomId][2] = 600;
        $timeId = Timer::add(1,function ()use(&$timeId,$roomId,$data){
            static $i = 5;
            $i--;
            Client::publish('webSocket',json_encode([
                'type'=>'toClient',
                'content'=>[
                    'toGroup'=>'room_'.$roomId,
                    'content'=>[
                        'type'=>'beforePlay',
                        'content'=>[
                            'second'=>$i
                        ]
                    ]
                ]
            ]),function (){});
            if($i==0){
                Timer::del($timeId);
                $redisDb = Redis::getRedisDb();
                $time = time();
                $redisDb->hSet('room_'.$roomId,'startTime',$time);
                $redisDb->hSet('room_'.$roomId.'_var','isStart',1);     //在redis记录棋赛已经开始了
                Client::publish('webSocket',json_encode([
                    'type'=>'toClient',
                    'content'=>[
                        'toGroup'=>'room_'.$roomId,
                        'content'=>[
                            'type'=>'playLog',
                            'content'=>[
                                'type'=>'logLeftRight',
                                'content'=>[
                                    'left'=>'开始时间',
                                    'right'=>date('Y-m-d h:i:s',$time)
                                ]
                            ]
                        ]
                    ]
                ]),function (){});
                self::countdown($data);
            }
        });

    }
    /*
     * description 比赛的倒计时
     * param $data = [ roomId:1,who:2 ]
     */
    static function countdown($data){
        $roomId = $data['roomId'];
        $who = $data['who'];
        self::$timeIdArr [$roomId][$who]= Timer::add(1,function ()use($roomId,$who){
            //检测棋赛状态，如果是已经退出或结束，则需要关闭定时器
            if(!self::$roomArr[$roomId])
            {
                Timer::del(self::$timeIdArr[$roomId][$who]);
            }
            Client::publish('webSocket',json_encode([
                'type'=>'toClient',
                'content'=>[
                    'toGroup'=>'room_'.$roomId,
                    'content'=>[                //以下的json数据是客户端收到的
                        'type'=>'countdown',
                        'content'=>[
                            'who'=>$who,
                            'time'=>self::$countdownArr[$roomId][$who]
                        ]
                    ]
                ]
            ]),function (){});
            self::$countdownArr[$roomId][$who]--;
        });
    }

    /**
     * @description 棋赛结束
     * @param $data = [ roomId ]
     */
    static function gameOver($data)
    {
        self::$roomArr[$data['roomId']] = false;
    }

    /**
     * @param $data = [ roomId:1,player:2]
     */
    static function otherCountdown($data)
    {
        $otherPlayer = 1;
        if($data['player'] == 1)
        {
            $otherPlayer = 2;
        }
        Timer::del(self::$timeIdArr[$data['roomId']][$data['player']]);

        self::countdown([
           'roomId'=>$data['roomId'],
            'who'=>$otherPlayer
        ]);
    }
   
}