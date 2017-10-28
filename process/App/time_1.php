<?php
use Workerman\Worker;
require_once   __DIR__.'/../workerman-for-win-master/Autoloader.php';
require_once __DIR__.'/../MyClass/RoomIndex.php';

$http_worker = new Worker();
$http_worker->name = 'time_1';
$http_worker->onWorkerStart = function()
{
    \MyClass\TimeIndex::init();
};

Worker::runAll();