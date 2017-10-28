<?php
namespace Home\Controller;
use Home\Lib\Redis;
use Think\Controller;

class UserController extends Controller{
    public function login()
    {
        $data = I('post.');
        if(!$data['username']||!$data['password'])
        {
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'用户名或密码错误'
            ]);
        }
        $redisDb = Redis::getDb();
        $exit = $redisDb->hGetAll('user_'.$data['username']);
        if(!$exit)
        {
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'用户名或密码错误'
            ]);
        }else{
            if($data['password'] == $exit['password']){
                session('username',$data['username']);
                $this->ajaxReturn([
                    'status'=>1
                ]);
            }
        }
        
    }
    public function register()
    {
        $data = I('post.');
        if(!$data['username']||!$data['password']) {
            $this->ajaxReturn([
                'status' => 0,
                'error' => '信息未填写完整'
            ]);
        }
        $redisDb = Redis::getDb();
        $exitUsername = $redisDb->hGetAll('user_'.$data['username']);
        if($exitUsername)
        {
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'用户名已存在'
            ]);
        }
        $addRes = $redisDb->hMset('user_'.$data['username'],[
            'createTime'=>time(),
            'password'=>$data['password']
        ]);
        if($addRes)
        {
            session('username',$data['username']);
            $this->ajaxReturn([
                'status'=>1,
            ]);
        }
    }
    public function userExit()
    {
        session(null);
        $this->redirect('Index/index',[],0,'');
    }
    public function setHttpUrl()
    {
        if(!$username = session('username'))
        {
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'非法请求'
            ]);
        }
        $data = I('post.');
        if(!$data['httpUrl']&&!$data['token']){
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'http不能为空'
            ]);
        }
        $res = $this->curl($data['httpUrl'],[
            'data'=>json_encode([
                'type'=>'validation',
                'content'=>null
            ])
        ]);
        if($res === false||$res != $data['token'])
        {
            $this->ajaxReturn([
                'status'=>0,
                'error'=>'字符串验证失败'
            ]);
        }
        $redisDb = Redis::getDb();
        $redisDb->hSet('user_'.$username,'httpUrl',$data['httpUrl']);
        $this->ajaxReturn([
            'status'=>1
        ]);
    }
    public function curl($httpUrl,$data)
    {
        $httpUrl = $httpUrl.'?'.http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$httpUrl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);  //表示不返回header信息
        $output = curl_exec($ch);
        curl_close($ch);
        if($output === FALSE ){
//            return "CURL Error:".curl_error($ch);
            return false;
        }else{
            return $output;
        }
        
    }
}