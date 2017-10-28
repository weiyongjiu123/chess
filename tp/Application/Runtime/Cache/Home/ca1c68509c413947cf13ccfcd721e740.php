<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>我的Bootstrap网页</title>
    <link href="/chess/Public/Home/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        div.container > div.row > div{
            border: 2px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <h4 style="text-align: center">登录表单</h4>
            <form action="" method="post" class="form-horizontal">
                <div class="form-group">
                    <label for="stu_num" class="col-sm-2 control-label">学号</label>
                    <div class="col-sm-10">
                        <input type="text" name="stu_num" id="stu_num" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="stu_pwd" class="col-sm-2 control-label">密码</label>
                    <div class="col-sm-10">
                        <input type="password" name="stu_pwd" id="stu_pwd" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox">记住我
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">登录</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!--<script src="/chess/Publicjs/jquery-3.2.1.min.js"></script>-->
<!--<script src="js/bootstrap.min.js"></script>-->
</body>
</html>