<?php if (!defined('THINK_PATH')) exit();?>
<html>
<head>
    <meta charset="utf-8">
    <title>身份认证 </title>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312">
    <link rel="stylesheet" href="http://class.sise.com.cn:7001/sise/css/style.css" type="text/css">
    <script type="text/javascript" src="http://class.sise.com.cn:7001/sise/js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="http://class.sise.com.cn:7001/sise/js/jquery.cookie.min.js"></script>
    <script type="text/javascript" src="http://class.sise.com.cn:7001/sise/js/encode.js?0"></script>
</head>
<script>
    function loginwithpwd() {
        if (document.all.username.value.length == 0) {
            alert("请输入用户名！");
            document.all.username.focus();
            return;
        }
        if (document.all.password.value.length == 0) {
            alert("请输入密码！");
            document.all.password.focus();
            return;
        }
        if ((document.all.password.value.length >0) && (document.all.username.value.length > 0)) {
            document.getElementById("Submit").disabled  = true;
            document.getElementById("Submit2").disabled  = true;
            form1.submit();
        }
    }
    function resetWin() {
        document.all.username.value = "";
        document.all.password.value = "";

        document.all.username.focus();
    }
    function Check_Nums() {
        if (event.keyCode == 13) {
            loginwithpwd();
        }
    }
    function goNext() {
        if (event.keyCode == 13) {
            form1.password.focus();
        }
    }
</script>
<body  text="#000000" topmargin="0" leftmargin="0">
<div align="center">
    <form name="form1" method="post" action="testPost">
        <input type="hidden" name="e89733b48af751cff06716902ee70574"  value="33fcefd4cd33316664884617c47a4a19">
        <input id="random"   type="hidden"  value="1506182591156"  name="random" />
        <input id="token"  type="hidden" name="token" />
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <!--<div> <a href="/sise/coursetemp/courseInfo.html" target="_blank"><b>2017-2018学年1学期 排课信息查看</b></a></div>
       <div><img alt="有新班" src="/sise/images/new.gif"><a href="addclass.txt" target="_blank"><b><font color="red">★★★增班信息查看</font></b></a></div> -->
        <div><font size="2" color="#006666">学号：</font><input name="username" id="username" type="text" size="15" class="notnull" onkeypress="goNext()" ></div>
        <div><font size="2" color="#006666">密码：</font><input name="password" id="password" type="password" size="15" class="notnull" onkeypress="Check_Nums()" ></div>
        <div><input type="button" id="Submit" name="Submit" value=" 登  录 " class="button" onclick="loginwithpwd();" onmouseover="this.style.color='red'" onmouseout="this.style.color='#1e7977'"><input type="button" id="Submit2" name="Submit2"  value=" 重  写 " class="button" onclick="resetWin();" onmouseover="this.style.color='red'" onmouseout="this.style.color='#1e7977'"></div>
    </form>
</div>
</body>
</html>