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
        .cp_black div:first-child span{
            color: green
        }
        .cp_white div:first-child span{
            color: green;
        }
    </style>
    <script>
//        $(function () {
//            $('.cp_play_msg').scrollTop($('.cp_play_msg')[0].scrollHeight );
//        });

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
            <div>白棋<span></span></div>
            <div><span>10</span><span>:</span><span>00</span></div>
        </div>
        <div class="cp_black">
            <div>黑棋<span></span></div>
            <div><span>10</span><span>:</span><span>00</span></div>
        </div>
        <div class="cp_play_msg">

        </div>
    </div>
    <!--<div><button onclick="test(1,60,60)">下棋</button> </div>-->
    <!--<div><button onclick="sendTest()">发送测试</button> </div>-->
、   <script>
        var canvas = document.getElementById('canvas');
        var ctn = canvas.getContext('2d');

        var chessData = []; //var chessData = new Array(15)
        var socketToken = <?php echo ($socketToken); ?>;
        var isPlay = false;
        var player = <?php echo ($play); ?>;   //下棋者类型，是白棋(1)还是黑棋(2)，0表示观看者
        var roomId = <?php echo ($roomId); ?>;
        var looker = '<?php echo ($looker); ?>';
        var whoStart = <?php echo ($whoStart); ?>;
        var chessImg = null;
        var otherChessImg = null;
        var isHasStart = <?php echo ($isHasStart); ?>;         //比赛是否已经开始了
        var gameStart = <?php echo ($gameStart); ?>;
        var logCount = 0;               //记录下棋信息数
        var playerStr = null;           //记录参赛者是白棋还是黑棋
        var whiteIsOnline = <?php echo ($room_var['whiteIsOnline']); ?>;       //记录白棋参赛者是否在线
        var blackIsOnline = <?php echo ($room_var['blackIsOnline']); ?>;       //记录黑棋参赛是否在线
        var isProgram = <?php echo ($isProgram); ?>;
        init();
        function init() {
            if(player == 1)
            {
                chessImg = new Image();
                chessImg.src = '/chess/Public/Picture/chess/white.png';
                otherChessImg = new Image();
                otherChessImg.src = '/chess/Public/Picture/chess/black.png';
                playerStr = '白棋';
            }else if(player == 2){
                chessImg = new Image();
                chessImg.src = '/chess/Public/Picture/chess/black.png';
                otherChessImg = new Image();
                otherChessImg.src = '/chess/Public/Picture/chess/white.png';
                playerStr = '黑棋';
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
            if(!isPlay)     //非参赛者和未轮到下棋的参赛者点击棋盘无反应
                return;
            var x = parseInt((e.clientX - 20) / 40);
            var y = parseInt((e.clientY - 20) / 40);

            if (chessData[x][y] != 0) {
                alert('你不能在这个位置下棋');
                return;
            }
            myDraw(x,y);
        }

        function myDraw(x,y) {
            drawChess(x,y,player);
            isPlay = false;             //参赛者下棋后就将不能再下棋，等对方下完后才能再下
            websocket.send(JSON.stringify({
                type:'toChess',
                content:{
                    toChess:'room'+roomId,
                    content:{
                        type:'play',
                        content:{
                            x:x,
                            y:y,
                            player:player,
                            roomId:roomId
                        }
                    }
                }
            }));
        }
        //绘制单个棋子
        function drawChess(x, y,who) {
            if (x >= 0 && x < 15 && y >= 0 && y < 15) {
                if(who == player)
                {
                    ctn.drawImage(chessImg, x * 40 + 20, y * 40 + 20);
                }else{
                    ctn.drawImage(otherChessImg, x * 40 + 20, y * 40 + 20);
                }
                chessData[x][y] = who;
            }
        }
        function playInit() {
            var whoStartStr = '比赛还没开始';
            if(whoStart == 1)
            {
                whoStartStr = '白棋'
            }else if(whoStart == 2){
                whoStartStr = '黑棋';
            }
            if(whoStart)
            {
                $('.cp_who_start').html('<span>从<span class="cp_who">'+whoStartStr+'</span>开始</span>'+'(你是'+playerStr+')');
            }else{
                $('.cp_who_start').html('<span style="color: red">'+whoStartStr+'</span>');
            }
        }

        (function () {
            if(isHasStart){
                $('#cp_right_first').html('即将开始，倒计时：<span class="cp_right_second" id="second"></span>');
            }
            //棋赛开始倒计时结束后
            if(gameStart)
            {
                if(player){
                    $('#cp_right_first').html('<button onclick="exitGame()">退出棋赛</button> ');
                }else{
                    $('#cp_right_first').text('棋赛已经开始了');
                }
                if(<?php echo ($room_var['nowWhoPlayer']); ?>  == player)
                {
                    if(isProgram)           //如果是程序下棋的，则程序所属的观看者不能下棋
                    {
                        isPlay = false;
                    }else{
                        isPlay = true;

                    }
                }
            }
            if(!whiteIsOnline)
            {
                $('.cp_white div:first-child span').text('(未进入)');
            }else{
                $('.cp_white div:first-child span').text('(已经入)');
            }
            if(!blackIsOnline)
            {
                $('.cp_black div:first-child span').text('(未进入)');
            }else{
                $('.cp_black div:first-child span').text('(已进入)');
            }

          playInit();

            websocket = new WebSocket('ws://127.0.0.1:7272');
            websocket.onopen = function (evt) {
                if(player)
                {
                    websocket.send(JSON.stringify({
                        type:'joinRoom',
                        content:{
                            'roomId':roomId,
                            socketToken:socketToken,
                            player:looker,
                            isPlayer:1,
                            looker:player
                        }
                    }))
                }else{
                    websocket.send(JSON.stringify({
                        type:'joinRoom',
                        content:{
                            'roomId':roomId,
                            isPlayer:0
                        }
                    }))
                }

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
            if(!isHasStart)
            {
                location.reload();
            }
            $('#second').text(data.second);
            if(data.second == '0'){
                setTimeout(function () {
                    gameStart = true;
                    if(whoStart == 1)
                    {
                        $('.cp_white div:first-child span').text('(正在思考中...)');
                    }else{
                        $('.cp_black div:first-child span').text('(正在思考中...)');
                    }
                    if(whoStart == player)
                    {
                        if(isProgram)
                        {
                            isPlay = false;
                        }else{
                            isPlay = true;
                        }
                    }
                    $('#cp_right_first').html('<button onclick="exitGame()">退出棋赛</button> ');
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
            logCount++;
            var str = '<div><span>'+logCount+'.</span><span>'+data.left+'</span><span>:</span><span>'+data.right+'</span></div>';
            $('.cp_play_msg').append(str);
            $('.cp_play_msg').scrollTop($('.cp_play_msg')[0].scrollHeight );
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
        //对方退出棋赛时相应该函数
        function theOtherLeave(data) {
            $.ajax({
                url:'/chess/index.php/Home/Game/exitedFromRoom',
                data:{
                    socketToken:socketToken,
                    player:looker,
                    roomId:roomId
                },
                dataType:'json',
                type:'post',
                success:function (res) {
                    if(res.status)
                    {
                        switch (parseInt(data.overType))
                        {
                            case 1:
                                logLeftRight({
                                    left:'<span style="color: red">棋赛结束</span>',
                                    right:''
                                });
                                alert('对方退出棋赛，比赛结束');
                                break;
                            case 2:
                                logLeftRight({
                                    left:'<span style="color: red">棋赛结束</span>',
                                    right:''
                                })
                        }
                        gameStart = true;

                    }else{
                        alert(res.error);
                    }
                }
            })
        }
        //退出棋赛
        function exitGame(){
            var otherPlayer = 1;
            if(player == 1)
                otherPlayer = 2;
            $.ajax({
                url:'/chess/index.php/Home/Game/exitGame',
                data:{
                    socketToken:socketToken,
                    player:looker,
                    roomId:roomId,
                    otherPlayer:otherPlayer
                },
                dataType:'json',
                type:'post',
                success:function (res) {
                    if(!res.status)
                    {
                        alert(res.error);
                    }else{
                        gameStart = false;
                        logLeftRight({
                            left:'<span style="color: red">棋赛结束</span>',
                            right:''
                        })
                        alert('已经退出房间');

                    }
                }
            })
        }
        //data = { x:1,y:2,player:3}
        function otherPlay(data) {
            var nowWhoPlay =1;
            var thePlayerStr = '黑棋';
            if(data.player == 1)
            {
                nowWhoPlay = 2;
                thePlayerStr = '白棋';
            }
            logLeftRight({left:thePlayerStr,right:'('+data.x+','+data.y+')'});
            whoCanPlay(nowWhoPlay);     //展示“谁在思考"
            drawChess(data.x,data.y,data.player);
            if(!player)
            {
                return;
            }
            if(data.player == player)
            {
                return;
            }
            if(isProgram)
            {
                isPlay = false;
            }else{
                isPlay = true;
            }
        }
        //判断轮到谁下棋
        function whoCanPlay(who) {
            if(who == 1){
                if(whiteIsOnline)
                {
                    $('.cp_white div:first-child span').text('(正在思考中...)');
                }else{
                    $('.cp_white div:first-child span').text('(未进入)');
                }
                if(blackIsOnline)
                {
                    $('.cp_black div:first-child span').text(null);
                }
            }else{
                if(blackIsOnline)
                {
                    $('.cp_black div:first-child span').text('(正在思考中...)');
                }else{
                    $('.cp_black div:first-child span').text(null);
                }
                if(whiteIsOnline)
                {
                    $('.cp_white div:first-child span').text(null);
                }
            }
        }
        //data={x:1,y:2,winner }
        function hasWin(data) {
            drawChess(data.x,data.y,data.winner);
            var winner = '白棋';
            $('.cp_white div:first-child span').text('(输了)');
            $('.cp_black div:first-child span').text('(输了)');
            if(data.winner == 2)
            {
                winner = '黑棋';
                $('.cp_black div:first-child span').text('(赢了)');
            }else{
                $('.cp_white div:first-child span').text('(赢了)');
            }
            logLeftRight({
                left:'棋赛结束',
                right:winner+'赢了'
            });
            alert(winner+'赢了');

        }
        /**
         * @description 参赛者进入棋赛触发的函数
         * @param data ={ looker:1 }
         */
        function playerComeIn(data) {
            if(data.player == 1)
            {
                $('.cp_white div:first-child span').text('（已进入）');
                whiteIsOnline = true;
            }else if(data.player == 2)
            {
                $('.cp_black div:first-child span').text('（已进入）');
                blackIsOnline = true;
            }
        }
        /**
         * @description 参赛离开时触发的函数
         * @param data = { player: 参赛者 }
         */
        function playerLeave(data) {
            if(data.player == 1)
            {
                $('.cp_white div:first-child span').text('(已离开)');
                whiteIsOnline = false;
            }else if(data.player == 2){
                $('.cp_black div:first-child span').text('(已离开)');
                blackIsOnline = true;
            }
        }
        /**
         * @description 远程访问服务器获取数据失败或数据格式错误
         * @param data = { error:错误信息，urlRes: 远程服务器返回的数据 ,player:参赛者}
         */
        function urlError(data) {
            var player = '白棋';
            if(data.player == 2)
            {
                player = '黑棋';
            }
            logLeftRight({
                left:'<span style="color: red">'+player+'</span>',
                right:'<span style="color: red">出现错误</span>'
            });
            logLeftRight({
                left:'<span style="color: red">远程服务返回的数据</span>',
                right:'<span style="color: red">'+data.urlRes+'</span>'
            });
            logLeftRight({
                left:'<span style="color: red">错误信息</span>',
                right:'<span style="color: red">'+data.error+'</span>'
            });
            if(data.count == 0)
            {
                logLeftRight({
                    left:'<span style="color: red">'+player+'</span>',
                    right:'<span style="color: red">第二次获取数据...</span>'
                });
            }
            if(data.count == 1)
            {
                logLeftRight({
                    left:'<span style="color: red">第二次获取数据失败</span>',
                    right:'<span style="color: red">终止棋赛</span>'
                });
            }

        }
    </script>
    <!--<script src="/chess/Public/Home/js/send.js"></script>-->
</body>
</html>