<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="/chess/Public/Home/bootstrap/js/jquery-3.0.0.min.js"></script>
    <title>五子棋PK</title>
    <style type="text/css">
        body
        {
            margin: 10px;
        }
        .cp_canvas{
            position: absolute;
            z-index: 100;
            width: 640px;
            float: left;
        }
        .cp_right{
            position: relative;
            left: 660px;
        }
        .cp_right_second{
            color: red;
        }
        .cp_white{
            margin-top: 40px;
        }
        .cp_white div:nth-child(2) span{
            font-size: 52px;
        }
        .cp_black{
            margin-top: 40px;
        }
        .cp_black div:nth-child(2) span{
            font-size: 52px;
        }
        .cp_who_start{
            margin-top: 10px;
        }
        .cp_who{
            color: green;
        }
        .cp_play_msg{
            margin-top: 20px;
            /*float: right;*/
            /*width: 10%;*/
            height: 300px;
            overflow:auto;
            /*background-color: red;*/
        }
    </style>
    <script>
        $(function () {
            $('.cp_play_msg').scrollTop($('.cp_play_msg')[0].scrollHeight );
        });

    </script>
</head>
<body>
    <div class="cp_canvas">
        <canvas width="640" id="canvas"  onmousedown="play(event)"  height="640">你的浏览器不支持HTML5 canvas  ，请使用 google chrome 浏览器 打开.
        </canvas>
    </div>
    <div class="cp_right">
        <div id="cp_right_first"></div>
        <div class="cp_who_start"></div>
        <div class="cp_white">
            <div>白棋</div>
            <div><span>00</span><span>:</span><span>00</span></div>
        </div>
        <div class="cp_black">
            <div>黑棋</div>
            <div><span>00</span><span>:</span><span>00</span></div>
        </div>
        <div class="cp_play_msg">

        </div>
    </div>
    <!--<div><button onclick="test(1,60,60)">下棋</button> </div>-->
    <!--<div><button onclick="sendTest()">发送测试</button> </div>-->
、   <script>
        var canvas = document.getElementById('canvas');
        var ctn = canvas.getContext('2d');

        var isWell = false;  // 是否赢了

//        var imgBlack = new Image();
//        imgBlack.src = '/chess/Public/Picture/chess/black.png';
//        var imgWhite = new Image();
//        imgWhite.src = '/chess/Public/Picture/chess/white.png';

        var chessData = []; //var chessData = new Array(15)
        var socketToken = <?php echo ($socketToken); ?>;
        var isPlay = true;
        var player = <?php echo ($play); ?>;   //下棋者类型，是白棋还是黑棋
        var roomId = <?php echo ($roomId); ?>;
        var looker = '<?php echo ($looker); ?>';
        var whoStart = <?php echo ($whoStart); ?>;
        var chessImg = null;
        var isHasStart = <?php echo ($isHasStart); ?>;         //比赛是否已经开始了
        init();
        function init() {
            if(!player){
                isPlay = false;
            }
            if(player == 1)
            {
                chessImg = new Image();
                chessImg.src = '/chess/Public/Picture/chess/white.png';
            }else if(player == 2){
                chessImg = new Image();
                chessImg.src = '/chess/Public/Picture/chess/black.png';
            }

            for (var i = 0; i <= 640; i += 40) {
                //绘制横线
                ctn.beginPath();
                ctn.moveTo(0, i);
                ctn.lineTo(640, i);
                ctn.closePath();
                ctn.stroke();
                //绘制竖线
                ctn.beginPath();
                ctn.moveTo(i, 0);
                ctn.lineTo(i, 640);
                ctn.closePath();
                ctn.stroke();
            }
            //初始化棋盘数组
            for (var x = 0; x < 15; x++) {
                chessData[x] = [];
                for (var y = 0; y < 15; y++) {
                    chessData[x][y] = 0;
                }
            }
        }
        //有些控制
        function play(e) {
            if(!isPlay)
                return;
            var x = parseInt((e.clientX - 20) / 40);
            var y = parseInt((e.clientY - 20) / 40);

            if (chessData[x][y] != 0) {
                alert('你不能在这个位置下棋');
                return;
            }
            drawChess(x,y);
        }
        //绘制单个棋子

        function drawChess(x, y) {
            if (x >= 0 && x < 15 && y >= 0 && y < 15) {
                ctn.drawImage(chessImg, x * 40 + 20, y * 40 + 20);
                chessData[x][y] = player;
            }
        }


//        function test(x,y) {
//            x = x*40 + 20;
//            y = y*40 + 20;
//            ctn.drawImage(imgWhite,x, y);
//        }
//        test(0,0);
//        test(0,1);
//        test(1,0);
//        test(20,20);
//        test(60,20);
//        test(20,60);
//        test(20,100);
        // ctn.drawImage(imgBlack,20, 20);
        // ctn.drawImage(imgBlack,60, 60);




        (function () {
            if(!isHasStart){
                $('#cp_right_first').html('即将开始，倒计时：<span class="cp_right_second" id="second">5</span>');
            }else{
                $('#cp_right_first').text('棋赛已经开始了');
            }
            var whoStartStr = '比赛还没开始';
            if(whoStart == 1)
            {
                whoStartStr = '白棋'
            }else if(whoStart == 2){
                whoStartStr = '黑棋';
            }
            if(whoStart)
            {
                $('.cp_who_start').html('<span>从<span class="cp_who">'+whoStartStr+'</span>开始</span>');
            }else{
                $('.cp_who_start').html('<span style="color: red">'+whoStartStr+'</span>');
            }

            websocket = new WebSocket('ws://127.0.0.1:7272');
            websocket.onopen = function (evt) {
                websocket.send(JSON.stringify({
                    type:'joinRoom',
                    content:{
                        'roomId':roomId
                    }
                }))
            };

            websocket.onclose = function (evt) {
                console.log("Disconnected");
            };

            websocket.onmessage = function (evt) {
                var data = JSON.parse(evt.data);
                call_user_func(data.type,[data.content])
            };

            websocket.onerror = function (evt, e) {
                console.log('Error occured: ' + evt.data);
            };
        })();

        //和php的call_user_func一样
        function call_user_func(cb, params) {
            func = window[cb];
            func.apply(cb, params);
        }
        //棋赛开始前的倒计时
        function beforePlay(data) {
            $('#second').text(data.second);
            if(data.second == '0'){
                setTimeout(function () {
//                    websocket.send(JSON.stringify({
//                        type:'toTime',
//                        content:{
//                            toTime:'time_1',
//                            content:{
//                                type:'startTime',
//                                content:null
//                            }
//                        }
//                    }));
                    $('#cp_right_first').text('棋赛开始');
                },1000)
            }
        }
        //
        function playLog(data) {
            call_user_func(data.type,[data.content]);
        }
        //拼接左右形式的字符串
        //data={left:1,right:2}
        function logLeftRight(data) {
            var str = '<div><span>'+data.left+'</span><span>:</span><span>'+data.right+'</span></div>';
            $('.cp_play_msg').append(str);
        }
        //将时间转化成日期格式
        function   formatDate(time)   {
            var now = new Date(time);
            var   year=now.getYear();
            var   month=now.getMonth()+1;
            var   date=now.getDate();
            var   hour=now.getHours();
            var   minute=now.getMinutes();
            var   second=now.getSeconds();
            return   year+"-"+month+"-"+date+"   "+hour+":"+minute+":"+second;
        }
        /**
         * description 倒计时
         * @param data ={ who:1,time:2 }
         */
        function countdown(data) {
            var time = parseInt(data.time);
            var who = parseInt(data.who);
            var minute = parseInt(time / 60);
            var second = time % 60;
            if(minute < 10)
            {
                minute = '0' + '' + minute;
            }
            if(second < 10)
            {
                second = '0' + '' + second;
            }
            if(who == 1)
            {
                $('.cp_white div:nth-child(2) span:first-child').text(minute);
                $('.cp_white div:nth-child(2) span:last-child').text(second);
            }else{
                $('.cp_black div:nth-child(2) span:first-child').text(minute);
                $('.cp_black div:nth-child(2) span:last-child').text(second);
            }
        }
    </script>
    <!--<script src="/chess/Public/Home/js/send.js"></script>-->
</body>
</html>