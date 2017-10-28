<?php
namespace Home\Controller;
use Home\Lib\Redis;
use Think\Controller;

class GameController extends Controller{
    public function joinGame()
    {
        $data = I('post.');
//        dd($data);
        //如果传过来的数据有任意一个为空，则返回错误信息
        if(!$data['player']||!$data['roomId']||!$data['playerType'])
        {
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'非法数据'
            ]);
        }
        //如果棋方的下棋者是人，且还有棋赛没结束，则不能进入其它棋赛
        $chessRoom = session('chessRoom');
        if($chessRoom && $chessRoom['isPersonPlay'])
        {
            $this->ajaxReturn([
               'status'=>0,
                'error'=>'你还有未完成的棋赛，不能进入其他棋赛'
            ]);
        }

        $username = session('username');
        //如果棋方下棋者是程序，则棋方必须得登陆
        if($data['playerType'] == 2 && !$username)
        {

            $this->ajaxReturn([
                'status'=>0,
                'error'=>'由程序下棋需登录'
            ]);
        }
        //如果棋方下棋者是程序且棋方已经登录，则还需设置自己的httpUrl
        if($data['playerType'] == 2 && $username)
        {
            $db = Redis::getDb();
            $httpUrl = $db->hGet('user_'.$username,'httpUrl');
            if(!$httpUrl)
            {
                $this->ajaxReturn([
                    'status'=>0,
                    'error' =>'你还未设置httpUrl'
                ]);
            }
        }
        $redisDb = Redis::getDb();
        
        if(!$username)
        {
            $username = 0;
        }
        
        if($data['player'] == 1)
        {
            $player = 'white';
            $otherPlayer = 'black';
        }else{
            $player = 'black';
            $otherPlayer = 'white';
        }
        $res = $redisDb->hGet('room_'.$data['roomId'],$player);
        if($res)        //判断该棋方有没有已经被人定下
        {
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'该棋方刚刚被别人定下',
                'action'=>1
            ]);
        }
        //以下开始更改数据
        $playerType = $player.'_player';
        $playerUsername = $player.'_username';
        $redisDb->hMset('room_'.$data['roomId'],[
           $playerType=>$data['playerType'],    //下棋者类型
            $player=>1,                         //棋方定下
            $playerUsername=>$username          //下棋的用户名（已注册）
        ]);
        //将已准备的棋方有关信息记录在session里
        $isPersonPlay = 0;
        if($data['playerType'] == 1)
        {
            $isPersonPlay = mt_rand(100000, 999999);
        }
        session('chessRoom',[
           $data['roomId'] =>[
               'playerType'=>$data['playerType'],      //棋方的下棋者类型，是程序还是人
               'player'=>$data['player'],              //棋方的类型，1表示白棋，2表示黑棋
           ],
            'isPersonPlay'=>$isPersonPlay            //在所有的棋赛房间是否有棋方的下棋者是人
        ]);
        $redisDb->set('room_'.$data['roomId']."_".$player,$isPersonPlay);
        //判断另外一个棋方是否已准备，如果已经准备好，则开始进入PK
        $res = $redisDb->hGet('room_'.$data['roomId'],$otherPlayer);
        if($res){
            //取随机数来选由谁开始下棋
            $rand = mt_rand(1,2);
            $redisDb->hMset('room_'.$data['roomId'],[
                'whoStart'=>$rand,
                'startTime'=>1          //标志棋赛开始倒计时已经启动
            ]);
            //记录棋赛开始时，轮到谁先下，也就是谁先下棋
            $redisDb->hSet('room_'.$data['roomId'].'_var','nowWhoPlayer',$rand);
            //通过中间站给webSocket服务发送信息，通知新五子棋要开始了
            $curlRes = curl('127.0.0.1:4237',[
                'data'=>json_encode([
                    'type'=>'forwarding',
                    'to'=>'room'.$data['roomId'],
                    'content'=>[
                        'type'=>'gameStart',
                        'content'=>[
                            'roomId'=>$data['roomId'],
                        ]
                    ]
                ])
            ]);
            if($curlRes != 'success')
            {
                $this->ajaxReturn([
                    'status'=>0,
                    'error'=>'棋赛开始失败，请重试'
                ]);
            }
//            dd($curlRes);
            //通知棋方即将进入五子棋PK
            $this->ajaxReturn([
                'status'=>2,
                'msg'=>'即将进入五子棋PK'
            ]);
        }
        $this->ajaxReturn([
            'status'=>2
        ]);

    }
    /*
     *  用户ajax请求退出棋赛,$_POST = [ socketToken:1,player:2,roomId:3,otherPlayer:4] 
     *  
     */
    public function exitGame()
    {
        $data = I('post.');
        $chessRoom = session('chessRoom');
        $redisDb = Redis::getDb();
        $socketToken = $redisDb->get($data['player']);
        if($socketToken != $data['socketToken'])        //验证用户是否是参赛者
        {
            $this->ajaxReturn([
               'status'=>0,
                'error'=>'非法请求'
            ]);
        }
        //下棋者必须是人
//        if($chessRoom[$data['roomId']]['playerType'] == 1)
//        {
            //清除请求退出赛琪者的session房间记录
            $chessRoom[$data['roomId']] = null;
            $chessRoom['isPersonPlay'] = null;
            session('chessRoom',$chessRoom);
           
//        }else{
//            $this->ajaxReturn([
//                'status'=>0,
//                'error'=>'非法请求'
//            ]);
//        }
        //将对应房间的参赛者SocketId（在redis中）都清除（因为要发信息通知另一个下棋者，故先不清除）
//        $redisDb->set('room_'.$data['roomId'].'_white_socketId',0);
//        $redisDb->set('room_'.$data['roomId'].'_black_socketId',0);
        //将房间设置为初始状态，即无人参赛
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
        //通过http通信站给chess进程发信息，告诉它有下棋者请求退出
        $res = curl('127.0.0.1:4237',[
            'data'=>json_encode(
                [
                    'type'=>'forwarding' ,
                    'to'=>'room'.$data['roomId'],
                    'content'=>[
                        'type'=>'exitGame',
                        'content'=>[
                            'roomId'=>$data['roomId'],
                            'otherPlayer'=>$data['otherPlayer']
                        ]
                    ]
                ]
            )
        ]);
        $this->ajaxReturn([
            'status'=>1,
        ]);
    }
    /*
     *  有一个下棋者退出棋赛，则另一个下棋者也被退出棋赛
     *  就会相应下列函数
     *  $_POST = [ socketToken:1,player:(socketToken在redis里的key),roomId:3]
     */
    public function exitedFromRoom()
    {
        $data = I('post.');
        $redis = Redis::getDb();
        $socketToken = $redis->get($data['player']);
        if($socketToken != $data['socketToken'])
        {
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'非法请求'
            ]);
        }
        $chessRoom = session('chessRoom');
        //下棋者必须是人
//        if($chessRoom[$data['roomId']]['playerType'] == 1)
//        {
            $chessRoom[$data['roomId']] = null;
            $chessRoom['isPersonPlay'] = null;
            session('chessRoom',$chessRoom);
//        }else{
//            $this->ajaxReturn([
//                'status'=>0,
//                'error'=>'非法请求'
//            ]);
//        }
        $this->ajaxReturn([
            'status'=>1,
        ]);
    }
}