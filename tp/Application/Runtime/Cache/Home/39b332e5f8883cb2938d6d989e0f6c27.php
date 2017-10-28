<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="/chess/Public/Home/bootstrap/js/jquery-3.0.0.min.js"></script>
    <link href="/chess/Public/Home/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>

        .cp_chess{
            float: left;
            margin-right: 50px;
        }
        .cp_chess div:nth-of-type(2){
            width: 200px;
            text-align: center;
        }
        .cp_bg_color{
            width:200px;
            height:200px;
            border-radius:20px;
            /*background-color: rgba(63,159,0,0.5);*/
            background-color: rgba(2,117,216,0.7);
        }
        .cp_bg_one{
            padding-top: 53px;
        }
        .cp_bg div:first-child div{
            width:50px;
            height:50px;
            border-radius:25px;
            float: left;
            cursor: pointer;
        }
        .cp_bg div:first-child div:first-child{
            color: black;
            margin-left: 17px;
        }
        .cp_bg div:first-child div:first-child[data-join='0']{
            background-color: rgb(112,112,112);
        }
        .cp_bg div:first-child div:first-child[data-join='1']{
            background-color: rgb(255,255,255);
        }
        .cp_bg div:first-child div:last-child{
            color: white;
            margin-left:60px;
        }
        .cp_bg div:first-child div:last-child[data-join='0']{
            background-color: rgb(112,112,112);
        }
        .cp_bg div:first-child div:last-child[data-join='1']{
            background-color: rgb(0,0,0);
        }
        .cp_bg_room{
            margin-top: 90px;
            font-size: 22px;
            text-align: center;
        }
        .rp_form_player_type span:first-child{
            margin-left: 13px;
            float: left;
        }
        .rp_form_player_type span:last-child{
            margin-right: 3px;
            float: right;
        }
        .rp_form_player_type{
            position: relative;
            top:-61px;
        }
    </style>
    <script>
        var white_join = false;
        var black_join = false;
        function joinGame(player,roomId,t) {
            if(player == 1)
            {
                var radioId1 = 'w_person_'+roomId;
                var radioId2 = 'w_cmp_'+roomId;
            }else{
                var radioId1 = 'b_person_'+roomId;
                var radioId2 = 'b_cmp_'+roomId;
            }
            var playerType = '';
            if($('#'+radioId1).is(":checked"))
            {
                playerType = $('#'+radioId1).val();
            }else if($('#'+radioId2).is(":checked")){
                playerType = $('#'+radioId2).val();
            }
            if(!playerType)
            {
                alert('请选择由谁下棋:人还是程序？');
                return;
            }
            $.ajax({
                url:'/chess/index.php/Home/Game/joinGame',
                data:{
                    player:player,
                    roomId:roomId,
                    playerType:playerType
                },
                dataType:'json',
                type:'post',
                success:function (res) {
                    if(!res.status)
                    {
                        alert(res.error);
                        if(res.action&&action == 1)
                        {
                            location.reload();
                        }
                    }else{
                        if(player == 1)
                        {
                            white_join = true;
                            $(t).css('background-color','rgb(225,225,225)');
                        }
                        else
                        {
                            black_join = true;
                            $(t).css('background-color','rgb(0,0,0)');
                        }
                        if(res.status == 2)
                        {
                            location.href = '/chess/index.php/Home/Index/chessPk/roomId/'+roomId;
//                            alert(res.msg);
//                            setTimeout(function () {
//                                location.href = '/chess/index.php/Home/Index/chessPk/roomId/'+roomId;
//                            },3000)
                        }
                    }
                }
            })
        }
        //父窗口调用子窗口的函数
        function fromParent(data) {
            call_user_func(data.function,[data.param]);
        }
        function call_user_func(cb, params) {
            func = window[cb];
            func.apply(cb, params);
        }

        $(function () {
            $('.cp_bg_one div:first-child[data-join=0]').mouseover(function () {
                if(white_join)
                        return;
                $(this).css('background-color','rgb(225,225,225)');
            });
            $('.cp_bg_one div:first-child[data-join=0]').mouseout(function () {
                if(white_join)
                    return;
                $(this).css('background-color','rgb(112,112,112)');
            });
            $('.cp_bg_one div:last-child[data-join=0]').mouseover(function () {
                if(black_join)
                    return;
                $(this).css('background-color','rgb(0,0,0)');
            });
            $('.cp_bg_one div:last-child[data-join=0]').mouseout(function () {
                if(black_join)
                        return;
                $(this).css('background-color','rgb(112,112,112)');
            });
            $('.cp_bg_one div:first-child').click(function () {
                if(white_join)
                        return;
                var roomId = $(this).parent().data('roomid');
                joinGame(1,roomId,this);
            });
            $('.cp_bg_one div:last-child').click(function () {
                if(black_join)
                        return;
                var roomId = $(this).parent().data('roomid');
                joinGame(2,roomId,this);
            });
        })
    </script>
</head>
<body>
    <div class="cp">
        <?php if(is_array($roomIdArr)): $i = 0; $__LIST__ = $roomIdArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="cp_chess">
                <div class="cp_bg_color">
                    x<div class="cp_bg">
                        <div class="cp_bg_one" data-roomId="<?php echo ($vo['roomId']); ?>">
                            <div data-join="<?php echo ($vo['white']); ?>"></div>
                            <div data-join="<?php echo ($vo['black']); ?>"></div>
                        </div>
                    </div>
                    <div class="cp_bg_room"><a href="/chess/index.php/Home/Index/chessPk/roomId/<?php echo ($vo['roomId']); ?>">房间<?php echo ($vo['roomId']); ?></a> </div>
                    <div class="rp_form_player_type">
                        <span>
                            <input
                                    <?php if($vo['white_player'] == 1): ?>checked<?php endif; ?>
                                    <?php echo ($vo['white_radio']); ?>
                                    type="radio" value="1"  name="playerType1_<?php echo ($vo['roomId']); ?>" id="w_person_<?php echo ($vo['roomId']); ?>"><label for="w_person_<?php echo ($vo['roomId']); ?>">人</label>
                            <input
                                    <?php echo ($vo['white_radio']); ?>
                                    <?php if($vo['white_player'] == 2): ?>checked<?php endif; ?>
                                    type="radio" value="2" name="playerType1_<?php echo ($vo['roomId']); ?>" id="w_cmp_<?php echo ($vo['roomId']); ?>"><label for="w_cmp_<?php echo ($vo['roomId']); ?>">程序</label>
                        </span>
                        <span>
                             <input
                                     <?php echo ($vo['black_radio']); ?>
                                     <?php if($vo['black_player'] == 1): ?>checked<?php endif; ?>
                                     type="radio" value="1" name="playerType2_<?php echo ($vo['roomId']); ?>" id="b_person_<?php echo ($vo['roomId']); ?>"><label for="b_person_<?php echo ($vo['roomId']); ?>">人</label>
                            <input
                                    <?php echo ($vo['black_radio']); ?>
                                    <?php if($vo['black_player'] == 2): ?>checked<?php endif; ?>
                                    type="radio" value="2" name="playerType2_<?php echo ($vo['roomId']); ?>" id="b_cmp_<?php echo ($vo['roomId']); ?>"><label for="b_cmp_<?php echo ($vo['roomId']); ?>">程序</label>
                        </span>

                    </div>
                </div>
                <div>
                    <span>观看<?php echo ($vo['onlineLooker']); ?></span>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</body>
</html>