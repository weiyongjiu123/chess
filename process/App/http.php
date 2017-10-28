<?php
use Workerman\Worker;
require_once   __DIR__.'/../workerman-for-win-master/Autoloader.php';
require_once __DIR__.'/../MyClass/HttpIndex.php';

$http_worker = new Worker('http://0.0.0.0:4237');
$http_worker->name = 'http';
$http_worker->onWorkerStart = function()
{
    \MyClass\HttpIndex::init();
};
$http_worker->onMessage = function ($connection, $data){
    $data = json_decode($_GET['data'],true);
    $data['connection'] = $connection;
    \MyClass\HttpIndex::msg($data);
};

Worker::runAll();