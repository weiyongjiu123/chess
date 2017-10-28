<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="/chess/Public/Home/bootstrap/js/jquery-3.0.0.min.js"></script>
    <link href="/chess/Public/Home/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cp_table{
            margin-top:100px;
            width: 910px;
        }
        .cp_table tr td:nth-child(3){
            width: 400px;
        }
        .cp_table tr td:nth-child(1){
            width: 85px;
            text-align: right;
        }
        .cp_table tr:nth-child(3) td button{
            margin-top: 4px;
            width: 100px;
            padding-top: 4px;
            padding-bottom: 4px;
        }
        .cp_table tr:nth-child(1) td:nth-child(3){
            color:red;
        }
        .cp_table tr:last-child td:nth-child(2){
            padding-top: 10px;
        }
        .cp_table tr:last-child td:nth-child(2) span{
            padding-top: 10px;
            color:green;
        }
    </style>
    <script>
        $(function () {
            $('.cp_table tr td button').click(function () {
                var url = $('#url').val();
                if(!url)
                {
                    $('.cp_form_error').text('http不能为空');
                    return;
                }
                var token = $('#token').val();
                if(!token)
                {
                    $('.cp_form_error').text('token不能为空');
                    return;
                }
                $.ajax({
                    url:'/chess/index.php/Home/User/setHttpUrl',
                    data:{httpUrl:url,token:token},
                    dataType:'json',
                    type:'post',
                    success:function(res){
                        if(!res.status)
                        {
                            $('.cp_form_error').text(res.error);
                        }else{
                            $('.cp_form_error').text(null);
                            $('.cp_success').text('设置成功');
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
        <div class="col-md-10">
           <table class="cp_table">
               <tr>
                   <td>http：</td>
                   <td><input class="form-control" placeholder="请输入url" id="url" value="<?php if(empty($httpUrl)): else: echo ($httpUrl); endif; ?>"></td>
                   <td>&nbsp;<span class="cp_form_error"></span></td>
               </tr>
               <tr style="height: 55px">
                   <td>验证字符串：</td>
                   <td><input class="form-control" id="token" value="token"></td>
                   <td></td>
               </tr>
               <tr>
                   <td></td>
                   <td><button class="btn-primary">确定</button> </td>
                   <td></td>
               </tr>
               <tr>
                   <td></td>
                   <td><span class="cp_success"></span></td>
                   <td></td>
               </tr>
           </table>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
</body>
</html>