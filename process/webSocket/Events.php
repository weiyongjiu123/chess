<?php
use \GatewayWorker\Lib\Gateway;
include_once 'AppClass/Index.php';
class Events
{
   public static function onWorkerStart($businessWorker){
       $index = new \AppClass\Index();
       $index->init();
       echo "worker has start\n";

   }
    public static function onConnect($client_id)
    {
        echo "connect sussecc\n";
    }
   public static function onMessage($client_id, $message)
   {
       $data = json_decode($message,true);
       $method = $data['type'];
       $data['content']['client_id'] = $client_id;
       \AppClass\Index::$method($data['content']);
   }
   
   /**
    * 当客户端断开连接时
    * @param integer $client_id 客户端id
    */
   public static function onClose($client_id)
   {
       // debug
//       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
       \AppClass\Index::decOnlineLooker();
//       print_r($_SESSION);
//       print_r($_SESSION[$    client_id]);
       // 从房间的客户端列表中删除
//       if(isset($_SESSION['room_id']))
//       {
//           $room_id = $_SESSION['room_id'];
//           $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
//           Gateway::sendToGroup($room_id, json_encode($new_message));
//       }
   }
  
}
