<?php
namespace MyClass;
use Channel\Client;
use Workerman\Lib\Timer;

require_once  __DIR__.'/../Other/ChannelClient.php';
require_once __DIR__.'/../MyClass/Redis.php';
require_once __DIR__.'/../MyClass/RoomPlay.php';
class RoomIndex{
    static private $isHasStart = false;         //标志棋赛是否已经开始
    static private $thisRoomId = 1;
    static private $whiteUrl = null;            //白棋服务器地址
    static private $blackUrl = null;            //黑棋服务器地址
    static private $nowWhoPlay = null;          //记录轮到谁下棋
    static private $chessPosition = [
        'x'=>null,
        'y'=>null
    ];       //记录这一步棋子的坐标（客户端坐标标准）
    static private $urlResError = [
        1=>'不是json字符串',
        2=>'json字符串中没有成员x或y ',
        3=>'x或y超界了',
        4=>'位置错误'
    ];
    static private $errorCount = 0;         //记录远程获取数据错误次数
    static private $getDataCount=2;         //获取远程数据最大的次数（出错的情况下），超过这个次数不再获取
    
    static function init(){
        Client::connect('127.0.0.1',2206);
        Client::on('room'.self::$thisRoomId,function ($data){
            $data = json_decode($data,true);
            $method = $data['type'];
            self::$method($data['content']);
        });
    }
    //tp通过http发送过来的通知
    static function gameStart($data)
    {
        //初始棋赛
        RoomPlay::init();
        $redis = Redis::getRedisDb();
        $res = $redis->hGetAll('room_'.$data['roomId']);
        //判断棋赛双方下棋者是否都是人
//        if($res['white_player'] == 1 && $res['black_player'] == 1)
//        {
           Client::publish('time_1',json_encode([
               'type'=>'beforePlay',
               'content'=>[
                   'roomId'=>$data['roomId'],
                   'who'=>$res['whoStart']
               ]
           ]),function (){});
            //标志棋赛已经开始了
            self::$isHasStart = true;
//            return;
//        }
//        print_r($res);
        if($res['white_player'] == 2)
        {
            if($res['whoStart'] == 1)
            {
                self::$whiteUrl = $redis->hGet('user_'.$res['white_username'],'httpUrl');
                Timer::add(6,function ()use($data){
                   self::programUrl([
                       'roomId'=>$data['roomId'],
                       'player'=>1,
                       'url'=>self::$whiteUrl
                   ]);
                },[],false);
            }
        }
        if($res['black_player'] == 2)
        {
            if($res['whoStart'] == 2)
            {
                self::$blackUrl = $redis->hGet('user_'.$res['black_username'],'httpUrl');
                Timer::add(6,function ()use($data){
                    self::programUrl([
                       'roomId'=>$data['roomId'],
                        'player'=>2,
                        'url'=>self::$blackUrl
                    ]);
                },[],false);
            }
        }
        //给参赛者远程服务器发棋赛开始的消息（下棋者是程序条件下）
        self::sendStartToUrl([
            'white'=>$res['white_player'],
            'black'=>$res['black_player']
        ]);
    }

    /*
     * description 棋赛结束类型：对方退出棋赛，棋赛结束
     * param $data = [ socketToken:(验证参赛者的字符串),player:(用户的类型),roomId:(房间号Id)]
     */
    static function exitGame($data)
    {
        //棋赛结束，把这一步棋子的坐标设置为初始状态
        self::$chessPosition['x'] = null;
        self::$chessPosition['y'] = null;
        $redisDb = Redis::getRedisDb();
        $otherPlayer = 'white';
        if($data['otherPlayer'] == 2)
        {
            $otherPlayer = 'black';
        }
        $otherPlayerSocketIdKey = 'room_'.$data['roomId'].'_'.$otherPlayer.'_socketId';
        $otherPlayerSocketId = $redisDb->get($otherPlayerSocketIdKey);
        if($otherPlayerSocketId)
        {
            Client::publish('webSocket',json_encode([
                'type'=>'toClientOne',
                'content'=>[
                    'client_id'=>$otherPlayerSocketId,
                    'content'=>[
                        'type'=>'theOtherLeave',
                        'content'=>[
                            'overType'=>1
                        ]
                    ]
                ]
            ]),function (){});
        }
        //标志棋赛已经结束或还没开始
        self::$isHasStart = false;
        Client::publish('time_1',json_encode([
            'type'=>'gameOver',
            'content'=>[
                'roomId'=>$data['roomId']
            ]
        ]),function (){});
        self::sendOverToUrl([
            'roomId'=>$data['roomId'],
            'overType'=>0,      //表示是中途结束比赛，未分出胜负
            'winner'=>0         //表示未分出胜负
        ]);
        self::$whiteUrl = null;     //这两个变量都是判断是否有程序下棋，游戏结束时必须把他们都设置初始状态
        self::$blackUrl = null;

    }
    /*
     * 参赛者发来的消息
     * $data = [ x,y,player,roomId]
     */
    static function play($data)
    {
        self::$errorCount = 0;      //针对于程序下棋，如果远程获取数据成功且数据格式正确，则将该变量设置回初始状态
        $redisDb = Redis::getRedisDb();
        //设置轮到谁下棋
        if($data['player'] == 1)
        {
            self::$nowWhoPlay = 2;
           $redisDb->hSet('room_'.self::$thisRoomId.'_var','nowWhoPlayer',2);
        }else{
            self::$nowWhoPlay = 1;
            $redisDb->hSet('room_'.self::$thisRoomId.'_var','nowWhoPlayer',1);
        }
        self::$chessPosition['x'] = $data['x'];
        self::$chessPosition['y'] = $data['y'];
        RoomPlay::updateChessArr([          //更新棋盘
            'x'=>$data['y'],
            'y'=>$data['x'],
            'player'=>$data['player']
        ]);

        $isWin = RoomPlay::isWin([      //判断是否已经成功组成五个棋
            'x'=>$data['y'],
            'y'=>$data['x'],
            'user'=>$data['player']
        ]);

        if($isWin === true)
        {
            //棋赛结束
            self::$isHasStart = false;
            //通知time进程结束本房间的棋赛倒计时
            Client::publish('time_1',json_encode([
                'type'=>'gameOver',
                'content'=>[
                    'roomId'=>$data['roomId']
                ]
            ]),function (){});
            //五子棋成功连成一条线，给客户端发消息
            Client::publish('webSocket',json_encode([
                'type'=>'toClient',
                'content'=>[
                    'toGroup'=>'room_'.$data['roomId'],
                    'content'=>[
                        'type'=>'hasWin',
                       'content'=>[
                           'winner'=>$data['player'],
                           'x'=>$data['x'],
                           'y'=>$data['y']
                       ]
                    ]
                ]
            ]),function(){});
            //结束棋赛，清除一些数据
            self::chessGameOver([
                'roomId'=>$data['roomId'],
                'winner'=>$data['player']

            ]);
        }else{

            //给客户端发消息
            Client::publish('webSocket',json_encode([
                'type'=>'toClient',
                'content'=>[
                    'toGroup'=>'room_'.$data['roomId'],
                    'content'=>[
                        'type'=>'otherPlay',
                        'content'=>[
                            'x'=>$data['x'],
                            'y'=>$data['y'],
                            'player'=>$data['player']
                        ]
                    ]
                ]
            ]),function (){});

            Client::publish('time_1',json_encode([
                'type'=>'otherCountdown',
                'content'=>[
                    'roomId'=>$data['roomId'],
                    'player'=>$data['player']
                ]
            ]),function (){});
        }

        if(self::$isHasStart)
        {

            //为了避免造成递归，内存占用大，所以采用进程通信相应的方法来调用自身的函数
            if(self::$nowWhoPlay == 1 && self::$whiteUrl)
            {
                Client::publish('room'.self::$thisRoomId,json_encode([
                    'type'=>'programUrl',
                    'content'=>[
                        'roomId'=>$data['roomId'],
                        'player'=>1,
                        'url'=>self::$whiteUrl
                    ]
                ]),function (){});
            }else if(self::$nowWhoPlay == 2 && self::$blackUrl)
            {
                Client::publish('room'.self::$thisRoomId,json_encode([
                    'type'=>'programUrl',
                    'content'=>[
                        'roomId'=>$data['roomId'],
                        'player'=>2,
                        'url'=>self::$blackUrl
                    ]
                ]),function (){});
            }
        }
//        print_r($isWin);
    }

    //访问黑棋的服务器  $data = [ roomId:1,player:2,url:3]
    static function programUrl($data)
    {
        //参赛者服务器访问，携带对手刚下的棋的坐标
        $urlRes = self::curl($data['url'],[
            'data'=>json_encode([
                'type'=>'play',
                'content'=>[
                    'x'=>self::$chessPosition['x'],
                    'y'=>self::$chessPosition['y']
                ]
            ])
        ],60);
        echo $urlRes."\n";
//        print_r($urlRes);
        if($urlRes === false)       //出错
        {
            echo "blackUrl fail \n";
            return;
        }
        $judgeRes = self::judgeUrlResData($urlRes);     //判断返回来的数据是否有错误
        if($judgeRes)       //出错
        {
            $error = self::$urlResError[$judgeRes];
            Client::publish('webSocket',json_encode([
                'type'=>'toClient',
                'content'=>[
                    'toGroup'=>'room_'.$data['roomId'],
                    'content'=>[            //以下是客户端接收到的数据
                        'type'=>'urlError',
                        'content'=>[
                            'error'=>$error,
                            'urlRes'=>$urlRes,
                            'player'=>$data['player'],
                            'count'=>self::$errorCount
                        ]
                    ]
                ]
            ]),function (){});
            self::$errorCount++;
            if(self::$errorCount >=self::$getDataCount){
                self::chessGameOver([
                    'roomId'=>$data['roomId'],
                    'winner'=>0
                ]);
                //通知time进程结束本房间的棋赛倒计时
                Client::publish('time_1',json_encode([
                    'type'=>'gameOver',
                    'content'=>[
                        'roomId'=>$data['roomId']
                    ]
                ]),function (){});
                echo "urlRes 4\n";
                return;
            }
            //重复获取远程数据,用进程通信方法调用自身的方法
            Client::publish('room'.self::$thisRoomId,json_encode([
                'type'=>'programUrl',
                'content'=>[
                    'roomId'=>$data['roomId'],
                    'player'=>$data['player'],
                    'url'=>$data['url']
                ]
            ]),function(){});
            return;
        }
        
        $dataRes = json_decode($urlRes,true);
        self::play([
            'x'=>$dataRes['x'],
            'y'=>$dataRes['y'],
            'player'=>$data['player'],
            'roomId'=>$data['roomId']
        ]);

    }
    //服务器访问
    static  function curl($httpUrl,$data,$time)
    {
        $httpUrl = $httpUrl . '?' . http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $httpUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,$time);       //设置超时时间
        curl_setopt($ch, CURLOPT_HEADER, 0);  //表示不返回header信息
        $output = curl_exec($ch);
        curl_close($ch);
        if ($output === FALSE) {
//            return "CURL Error:".curl_error($ch);
            return false;
        } else {
            return $output;
        }
    }
    //设置参赛者的进入房间状态 $data = [ player:1 ]
    static function setPlayer($data)
    {
        $redisDb = Redis::getRedisDb();
        if($data['player']== 1)
        {
            $redisDb->hSet('room_'.self::$thisRoomId.'_var','whiteIsOnline',1);
        }else if($data['player']== 2){
            $redisDb->hSet('room_'.self::$thisRoomId.'_var','blackIsOnline',1);
        }
    }
    //设置参赛离开房间状态 $data = [ player:1 ]
    static function setPlayerLeave($data)
    {
        $redisDb = Redis::getRedisDb();
        if($data['player']== 1)
        {
            $redisDb->hSet('room_'.self::$thisRoomId.'_var','whiteIsOnline',0);
        }else if($data['player']== 2){
            $redisDb->hSet('room_'.self::$thisRoomId.'_var','blackIsOnline',0);
        }
    }
    //退出棋赛类型：棋赛中某一方获胜，结束比赛。$data={ roomId:1,winner:0表示中途结束棋赛，未分出胜负 }
    static function chessGameOver($data)
    {
        //棋赛结束，把这个步棋的坐标设置为初始状态
        self::$chessPosition['x'] = null;
        self::$chessPosition['y'] = null;
        $redisDb = Redis::getRedisDb();
        //和棋赛房间有关的信息都设为初始状态
        $redisDb->hMset('room_'.$data['roomId'],[
            'white'=>0,
            'black'=>0,
            'white_player'=>0,
            'black_player'=>0,
            'black_username'=>0,
            'white_username'=>0,
            'whoStart'=>0,
            'startTime'=>0
        ]);
        $redisDb->hMset('room_'.$data['roomId'].'_var',[
            'blackIsOnline'=>0,
            'whiteIsOnline'=>0,
            'nowWhoPlayer'=>0,
            'isStart'=>0
        ]);
        //发给白棋，通知他游戏结束，退出房间
        Client::publish('webSocket',json_encode([
            'type'=>'toClientOne',
            'content'=>[
                'client_id'=>$redisDb->get('room_'.$data['roomId'].'_white_socketId'),
                'content'=>[
                    'type'=>'theOtherLeave',
                    'content'=>[
                        'overType'=>2
                    ]
                ]
            ]
        ]),function (){});
        //发给黑棋，通知他棋赛结束，退出房间
        Client::publish('webSocket',json_encode([
            'type'=>'toClientOne',
            'content'=>[
                'client_id'=>$redisDb->get('room_'.$data['roomId'].'_black_socketId'),
                'content'=>[
                    'type'=>'theOtherLeave',
                    'content'=>[
                        'overType'=>2
                    ]
                ]
            ]
        ]),function (){});
        if($data['winner'])
        {
            self::sendOverToUrl([
                'roomId'=>$data['roomId'],
                'overType'=>1,           //表示已分出胜负
                'winner'=>$data['winner']
            ]);
        }else{
            self::sendOverToUrl([
                'roomId'=>$data['roomId'],
                'overType'=>0,           //表示未分出胜负
                'winner'=>0
            ]);
        }

        self::$whiteUrl = null;
        self::$blackUrl = null;
    }
    static function judgeUrlResData($resData)
    {
        $data = json_decode($resData,true);
        if(is_null($data))      //判断是否是json字符串
        {
            return 1;       //状态1：不是json字符串
        }
        if((!isset($data['x']))||(!isset($data['y'])))
        {
            return 2;               //状态2：json字符串中没有成员x或y 
        }
        if(($data['x'] <0 || $data['x'] >14)||($data['y'] < 0 || $data['y'] > 14))
        {
            return 3;       //状态3：x或y超界了
        }
        $isPositionErrorRes = RoomPlay::isPositionError([
            'x'=>$data['y'],
            'y'=>$data['x']
        ]);
        if($isPositionErrorRes)
        {
            return 4;       //状态4，位置错误
        }
        return 0;       //状态0，无错误
    }

    /**
     * @description 棋赛结束，发通知给远程服务器（下棋者是程序的）
     * @param $data = [ roomId:1,overType:2 [,winner:3]]
     */
    static function sendOverToUrl($data){
        $overType = [
            0=>'未分出胜负',
            1=>'已分出胜负'
        ];
        $payerType = [
            0=>'未分出胜负',
            1=>'白棋',
            2=>'黑棋'
        ];
        if(self::$whiteUrl)
        {
            $urlRes = self::curl(self::$whiteUrl,[
                'data'=>json_encode([
                    'type'=>'gameOver',
                    'content'=>[
                        'overType'=>$data['overType'],
                        'overMsg'=>$overType[$data['overType']],
                        'winnerType'=>$data['winner'],
                        'winnerMsg'=>$payerType[$data['winner']]
                    ]
                ])
            ],3);
        }
        if(self::$blackUrl)
        {
            $urlRes = self::curl(self::$blackUrl,[
                'data'=>json_encode([
                    'type'=>'gameOver',
                    'content'=>[
                        'overType'=>$data['overType'],
                        'overMsg'=>$overType[$data['overType']],
                        'winnerType'=>$data['winner'],
                        'winnerMsg'=>$payerType[$data['winner']]
                    ]
                ])
            ],3);
        }
    }

    /**
     * @description 通知远程服务器（在下棋者是程序的情况下）
     * @param $data = [ white:1,black:2 ]
     */
    static function sendStartToUrl($data)
    {
        $player = [
            1=>'人',
            2=>'程序'
        ];
        if(self::$whiteUrl)
        {
            self::curl(self::$whiteUrl,[
                'data'=>json_encode([
                    'type'=>'gameStart',
                    'content'=>[
                        'otherPlayer'=>$data['black'],
                        'otherPlayerMsg'=>$player[$data['black']]
                    ]
                ])
            ],2);
        }
        if(self::$blackUrl)
        {
            self::curl(self::$blackUrl,[
                'data'=>json_encode([
                    'type'=>'gameStart',
                    'content'=>[
                        'otherPlayer'=>$data['white'],
                        'otherPlayerMsg'=>$player[$data['white']]
                    ]
                ])
            ],2);
        }
    }
}