<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="/chess/Public/Home/bootstrap/js/jquery-3.0.0.min.js"></script>
    <link href="/chess/Public/Home/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cp_md_form{
            margin-top:50px;
        }
        .cp_md_form div span{
            position:relative;
            top: 33px;
            left:7px;
            font-size: 22px;
        }
        .cp_md_form div input{
            width: 200px;
            padding-left:33px;
        }
        .cp_md_form div button{
            margin-top: 16px;

            width: 200px;
        }
        .cp_md_form .cp_md_error{
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
    <script>
        $(function () {
            $('.cp_md_form button').click(function () {
                var username = $('.cp_md_form input[name=username]').val();
                var password = $('.cp_md_form input[name=password]').val();
//                if(!username)
//                {
//                    $('.cp_md_error').text('用户名未填写');
//                    return;
//                }
//                if(!password)
//                {
//                    $('.cp_md_error').text('密码未填写');
//                    return;
//                }
//                alert(33);
                $.ajax({
                    url:'/chess/index.php/Home/user/login',
                    type:'post',
                    dataType:'json',
                    data:{
                        username:username,
                        password:password
                    },
                    success:function (res) {
                        if(!res.status)
                        {
                            $('.cp_md_error').text(res.error);
                        }else{
                            parent.location.reload();
                        }
                    }
                })
            })
        })
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-1">

        </div>
        <div class="col-md-11">
            <div class="cp_md_form">
                <div>
                    <span class="cp_md_error"></span>
                </div>
                <div>
                    <span  class="glyphicon glyphicon-user cp_md_userame"></span>
                    <input type="text" class="form-control" placeholder="用户名" name="username">
                </div>
                <div>
                    <span  class="glyphicon glyphicon-lock cp_md_userame"></span>
                    <input type="password" class="form-control" placeholder="密码" name="password">
                </div>
                <div>
                    <button type="button" class="btn btn-primary">登陆</button>
                </div>
            </div>

        </div>
        <!--<div class="col-md-7">.col-md-4</div>-->
    </div>
</div>
</body>
</html>