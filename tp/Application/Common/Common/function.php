<?php
function dd($content,$head = '')
{
    $fileName = 'test.txt';
    $f = fopen($fileName,'a+') or die('写入文件出现错误');
    $time = date('Y-m-d H:i:s',time());
    $content = print_r($content,true);
    $content = "\n\r------------------------------------- $head  $time --------------------------------------------------------\n\r".$content;
    $content = $content."\n\r------------------------------------------------------------------------------------------------\n\r";
    fwrite($f,$content);

}
function curl($httpUrl,$data)
{
    $httpUrl = $httpUrl . '?' . http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $httpUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);  //表示不返回header信息
    $output = curl_exec($ch);
    curl_close($ch);
    if ($output === FALSE) {
//            return "CURL Error:".curl_error($ch);
        return false;
    } else {
        return $output;
    }
}