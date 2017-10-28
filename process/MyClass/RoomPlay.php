<?php
namespace MyClass;
class RoomPlay{
    static private $top = 0;
    static private $buttom = 14;
    static private $left = 0;
    static private  $right = 14;
    static private $chessArr;
    static function init()
    {
        //初始或棋盘
        for($i=0;$i<=14;$i++)
        {
            for ($j=0;$j<=14;$j++)
            {
                self::$chessArr[$i][$j] = 0;
            }
        }

    }
    /*
     * 判断是五子练成功
     * $arr 棋盘数组
     * $position = [ x:1,y:2,user:3]
     */
    public static function judge($arr,$position){
        $x = $position['x'];
        $y = $position['y'];
        $top = 0;
        $bottom = 0;
        $left = 0;
        $right = 0;
        $leftTop = 0;
        $leftBottom = 0;
        $rightTop = 0;
        $rightBottom = 0;
        $i = $position['x'];
        while (true){
            $i--;
            if($i<self::$top)
            {
                break;
            }
            if($arr[$i][$y] == $position['user'])
            {
                $top++;
                if($top == 4){
                    return true;
                }
            }else{
                break;
            }
        }
        $i = $position['x'];
        while (true){
            $i++;
            if($i > self::$buttom)
                break;
            if($arr[$i][$y] == $position['user'])
            {
                $bottom++;
                if($bottom + $top >= 4){
                    return true;
                }
            }else{
                break;
            }
        }
        $i = $y;
        while (true){
            $i--;
            if($i<self::$left)
                break;
            if($arr[$x][$i] == $position['user'])
            {
                $left++;
                if($left==4)
                {
                    return true;
                }
            }else{
                break;
            }
        }

        $i = $y;
        while (true)
        {
            $i++;
            if($i>self::$right)
                break;
//            print_r($arr[$x][$i]);
            if($arr[$x][$i] == $position['user'])
            {
                $right++;
                if($right + $left >= 4)
                    return true;
            }else{
                break;
            }
        }
        //左上角和右下角
        $i = $x;
        $j = $y;
        while (true){
            $i--;
            $j--;
            if($i<self::$top||$j<self::$left)
                break;
            if($arr[$i][$j] == $position['user'])
            {
                $leftTop++;
                if($leftTop == 4)
                {
                    return true;
                }
            }else{
                break;
            }
        }
        $i = $x;
        $j = $y;
        while (true){
            $i++;
            $j++;
            if($i>self::$buttom||$j>self::$right)
                break;
            if($arr[$i][$j] == $position['user'])
            {
                $rightBottom++;
                if($rightBottom + $leftBottom >= 4)
                {
                    return true;
                }
            }else{
                break;
            }
        }
        //右上角和左下角
        $i = $x;
        $j = $y;
        while (true)
        {
            $i--;
            $j++;
            if($i<self::$top||$j>self::$right)
                break;
            if($arr[$i][$j] == $position['user'])
            {
                $rightTop++;
                if($rightTop == 4)
                    return true;
            }else{
                break;
            }
        }
        $i = $x;
        $j = $y;
        while (true)
        {
            $i++;
            $j--;
            if($i>self::$buttom || $j<self::$left)
                break;
            if($arr[$i][$j] == $position['user'])
            {
                $leftBottom++;
                if($leftBottom + $rightTop >= 4)
                {
                    return true;
                }
            }else{
                break;
            }
        }
    }
    /*
     * 更新棋盘数组
     *  $data = [ x:1,y:2,player:3]
     */
    public static function updateChessArr($data)
    {
        self::$chessArr[$data['x']][$data['y']] = $data['player'];
    }
    //判断是否获胜
    public static function isWin($data){
        return self::judge(self::$chessArr,$data);
    }
    //判断程序下的棋子的位置是否有错误  $data = [ x:1,y:2 ]
    public static function isPositionError($data)
    {
        $x = $data['x'];
        $y = $data['y'];
        if(self::$chessArr[$x][$y] == 0)
        {
            return false;       //位置正确
        }else{
            return true;        //位置错误
        }
    }
}