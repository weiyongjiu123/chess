<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>我的Bootstrap网页</title>
    <link href="/chess/Public/Home/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .my_navbar_span{
            color: blue;
            font-size: 39px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <a href="lab04.html" class="navbar-brand"><span class="my_navbar_span">B</span></a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse">

            <ul class="nav navbar-nav">
                <li><a href="#">相册</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        音乐<span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">中文音乐</a> </li>
                        <li><a href="#">英文音乐</a> </li>
                        <li class="divider"></li>
                        <li><a href="#">校园音乐</a> </li>
                    </ul>
                </li>
                <li><a href="#">视频</a></li>
            </ul>

            <form action="" method="post" class="navbar-form navbar-right">
                <div class="input-group">
                    <input type="text" name="username" class="form-control my_input" placeholder="用户名" style="width: 130px;border-radius:8px">
                </div>
                <div class="input-group">
                    <input type="text" name="username" class="form-control my_input" placeholder="用户名"  style="width: 130px;border-radius:8px">
                </div>
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-primary" style="border-radius: 8px">搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</nav>
<script src="/chess/Public/Home/bootstrap/js/jquery-3.0.0.min.js"></script>
<script src="/chess/Public/Home/bootstrap/js/bootstrap.min.js"></script>

</body>
</html>