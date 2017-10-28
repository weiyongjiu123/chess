<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>我的Bootstrap网页</title>
    <link href="/chess/Public/Home/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <a href="lab04.html" class="navbar-brand">天天商城</a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse">

            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        手机
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">苹果</a></li>
                        <li><a href="#">安卓</a></li>
                        <li class="divider"></li>
                        <li><a href="#">全面屏</a></li>
                        <li><a href="#">双摄</a></li>
                    </ul>
                </li>
                <li><a href="#">电器</a></li>
                <li><a href="#">超市</a></li>
            </ul>

            <form action="" method="post" class="navbar-form navbar-left">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-primary">搜索</button>
                    </div>
                </div>
            </form>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">购物车</a></li>
                <li><a href="#">订单</a></li>
                <li><a href="#">客服</a></li>
            </ul>


        </div>
    </div>
</nav>
<script src="/chess/Public/Home/bootstrap/js/jquery-3.0.0.min.js"></script>
<script src="/chess/Public/Home/bootstrap/js/bootstrap.min.js"></script>

</body>
</html>