<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="/chess/Public/Home/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <p></p>
    <div class="progress progress-striped active progress-warning">
        <div class="progress-bar" style="width: 0%"></div>
    </div>
</div>

<script src="/chess/Public/Home/bootstrap/js/jquery-3.0.0.min.js"></script>
<script src="/chess/Public/Home/bootstrap/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        var totalTime  = 5000;
        var elapsedTime = 0;
        var updateInterval = 500;
        var percent;
        var intervalId = window.setInterval(function () {
            elapsedTime += updateInterval;
            percent = Math.round(elapsedTime / totalTime *100);
            $('.progress-bar').css('width',percent+'%').text(percent+'%');
            if(elapsedTime >= totalTime)
            {
                window.clearInterval(intervalId);
            }
        },updateInterval);
    })
</script>
</body>
</html>