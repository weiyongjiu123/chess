<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="/chess/Public/Home/bootstrap/js/jquery-3.0.0.min.js"></script>
    <script src="/chess/Public/Home/bootstrap/js/bootstrap.min.js"></script>
    <link href="/chess/Public/Home/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *{
            margin: 0px;
            padding: 0px;
        }
        .cp_head{
            width: 100%;
            padding-top:10px;
            padding-bottom: 10px;
            background-color: rgb(2,117,216);
            color: #ffffff;
            text-align: right;
        }

        .cp_content span{
            margin-right:45px;
        }
        .cp_md_one {
            margin-top: 100px;
            font-size: 18px;
            margin-left: 40px;
        }
        .cp_md_one div{
            margin-top: 10px;
        }
        .cp_md_two{
            margin-top:100px;
        }

    </style>
    <script>
        function sendToClild() {
            var iframeObj = document.getElementById('myIframe');
            iframeObj.contentWindow.fromParent({
                'function':'ttt',
                'param':'sss'
            });
        }
//        $(function () {
//            setInterval(function () {
//                $.ajax({
//                    url:'/chess/index.php/Home/Index/ping',
//                    type:'post',
//                    dataType:'json',
//                    success:function (res) {
//                        console.log(22)
//                    }
//                })
//            },1000);
//        })
    </script>
</head>
<body>
<div class="cp_head">
    <div class="cp_content">
        <span>
            <?php if($_SESSION['username']): ?>你好，<?php echo ($_SESSION['username']); ?>
                <a href="/chess/index.php/Home/user/userExit" style="color:#ffffff">&nbsp;退出</a>
                <?php else: ?>
                <a href="/chess/index.php/Home/Index/login" target="iframeName" style="color: #ffffff">登陆</a>/
                <a href="/chess/index.php/Home/Index/register" target="iframeName" style="color: #ffffff">注册</a><?php endif; ?>
        </span>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="cp_md_one">
                <div><a href="/chess/index.php/Home/Index/home" target="iframeName">首页</a></div>
                <div><a href="/chess/index.php/Home/Index/mySetting" target="iframeName">我的设置</a></div>
                <!--<div><button onclick="sendToHome()">调用</button> </div>-->
            </div>
        </div>
        <div class="col-md-9">
            <div class="cp_md_two">
                <iframe  name="iframeName" id="myIframe" scrolling="no" frameborder="0" src="/chess/index.php/Home/Index/home" width="100%" height="650px"></iframe>
            </div>
        </div>

    </div>
</div>
</body>
</html>