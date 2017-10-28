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
        .cp_md_form div input{
            width: 200px;
        }
        .cp_md_form div button{
            margin-top: 10px;
        }
        .cp_md_form div {
            margin-top: 10px;
        }
        .cp_md_form button{
            width: 200px;
        }
        .cp_md_form .cp_md_error{
            color:red;
        }

    </style>
   <script>
       $(function () {
          $('.cp_md_form button').click(function () {
              var username = $('.cp_md_form input[name=username]').val();
              var password = $('.cp_md_form input[name=password]').val();
              var password1 = $('.cp_md_form input[name=password1]').val();
              if(!username)
              {
                  $('.cp_md_error').text('用户名不能为空');
                  return;
              }
              if(!password)
              {
                  $('.cp_md_error').text('密码不能为空');
                  return;
              }
              if(password.length <6)
              {
                  $('.cp_md_error').text('密码长度需要大于6位');
                  return;
              }
              if(password != password1)
              {
                  $('.cp_md_error').text('两次密码输入不一样');
                  return;
              }

              $.ajax({
                  url:'/chess/index.php/Home/user/register',
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
        <div class="col-md-4">
            <div class="cp_md_form">
                <div><span class="cp_md_error"></span></div>
                <div>
                    <input type="text" class="form-control" placeholder="用户名" width="100px" name="username">
                </div>
                <div>
                    <input type="password" class="form-control" placeholder="密码" width="100px" name="password">
                </div>
                <div>
                    <input type="password" class="form-control" placeholder="再次认密码" width="100px" name="password1">
                </div>
                <div>
                    <button type="button" class="btn btn-primary">注册</button>
                </div>
            </div>

        </div>
        <div class="col-md-7"></div>
    </div>
</div>
</body>
</html>