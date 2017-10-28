<?php
namespace Home\Controller;
use Home\Lib\Redis;
use Think\Controller;
class IndexController extends Controller
{
    public function index()
    {
        $this->display();
    }

    public function chessPk($roomId)
    {
        $redisDb = Redis::getDb();
//        $chessStartTime = $redisDb->hGet('room_'.$roomId,'startTime');
        $room_var = $redisDb->hGetAll('room_'.$roomId.'_var');             //获取房间各种的状态
//        print_r($room_var);
        $roomRes = $redisDb->hGetAll('room_'.$roomId);
        $isHasStart = 0;        //标志倒计时是否已经开始
        $gameStart = 0;
        if($roomRes['startTime'])
        {
            $isHasStart = 1;
        }
        if($roomRes['startTime'] > 9)
        {
            $gameStart = 1;
        }
//        print_r($roomRes['startTime'] > 10);
//        print_r($isHasStart);
//        print_r($gameStart);
        $socketToken = 0;
        $roomArr = session('chessRoom');
        $player = 0;                    //0表示观看者，white表示白棋，black表示黑棋
        $play = 0;                  //下棋者类型，1表示白棋，2表示黑棋，0表示非参赛者
        //判断观看者是否是棋赛的参赛者
        if($roomArr[$roomId])
        {
            $player = 'white';
            if($roomArr[$roomId]['player'] == 2)
            {
                $player = 'black';
            }
            //如果redis对应的棋位已经没有了，需要跟新下棋者的session记录
            if($roomRes[$player] != 1)
            {
                $roomArr[$roomId] = null;
                $roomArr['isPersonPlay'] = null;
                session('chessRoom',$roomArr);
                $player = 0;        //跟新$player的值,0表示是观看者
            }else{
                $play = $roomArr[$roomId]['player'];
                $socketToken = $roomArr['isPersonPlay'];
            }

        }

        $isProgram = 0;

        if($roomArr[$roomId]['playerType']&&$roomArr[$roomId]['playerType'] == 2)
        {
            $isProgram = 1;
        }

        $res = $redisDb->hGet('room_'.$roomId,'whoStart');
        $whoStart = 0;
        if($res)
        {
            $whoStart = $res;       //0表示观看者，不可下棋
        }
        $this->assign('gameStart',$gameStart);
        $this->assign('isHasStart',$isHasStart);
        $this->assign('play',$play);
        $this->assign('whoStart',$whoStart);
        $this->assign('roomId',$roomId);
        $this->assign('socketToken',$socketToken);
        $this->assign('looker','room_'.$roomId.'_'.$player);
        $this->assign('room_var',$room_var);
        $this->assign('isProgram',$isProgram);
        $this->display();
    }

    public function home()
    {
        $redisDb = Redis::getDb();
        $roomIdArr = $redisDb->sMembers('allRoom');
        foreach ($roomIdArr as $value)
        {
            $room = $redisDb->hGetAll('room_'.$value);
            $room['white_radio'] = '';
            $room['black_radio'] = '';
            if($room['white_player'])
            {
                $room['white_radio'] = 'disabled';
            }
            if($room['black_player'])
            {
                $room['black_radio'] = 'disabled';
            }
            $room['roomId'] = $value;
            $roomArr[] = $room;
        }
        $this->assign('roomIdArr',$roomArr);
        $this->display();
    }

    public function register()
    {
        $this->display();
    }

    public function login()
    {
        $this->display();
    }

    public function test()
    {
        $redisDb = Redis::getDb();
        $arr= $redisDb->keys('*');
        print_r($arr);
//        phpinfo();
    }
    public function mySetting()
    {
        if(!session('?username'))
        {
            $this->redirect('login',[],0,'');
        }else{
            $redisDb = Redis::getDb();
            $httpUrl = $redisDb->hGet('user_'.session('username'),'httpUrl');
            $this->assign('httpUrl',$httpUrl);
        }
        $this->display();
    }
    public function getSession(){
        echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
    }
    public  function desSession()
    {
        session(null);
        print_r('session已经完全消除');
    }
    public function ping()
    {
        $this->ajaxReturn([
            'status'=>1,
            'msg'=>'pong'
        ]);
    }

}