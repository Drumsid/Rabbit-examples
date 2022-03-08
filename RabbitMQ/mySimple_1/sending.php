<?php

/*
*    Первый пример с офф сайта. Этот файл отправляет сообщения
*    receive.php принимает сообщение.
*    Работает так:
*    Запускаем в консоли докера чтоб отправить сообщение команду
*    `make sending_s_1`
*    Чтоб получить сообщения запускаем
*    `make receive_s_1`
*/

require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const MIN_INTEGER = 1;
const MAX_INTEGER = 10000;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('queueA', false, false, false, false);
$channel->queue_declare('queueB', false, false, false, false);

$firstNum = rand(MIN_INTEGER, MAX_INTEGER);
$secondNum = rand(MIN_INTEGER, MAX_INTEGER);

$arrNums = ['firstNum' => $firstNum, 'secondNum' => $secondNum];
$jsonData = json_encode($arrNums);

$msg = new AMQPMessage($jsonData);
if ($firstNum % 2 == 1) {
    $channel->basic_publish($msg, '', 'queueA');
    echo " [x] Sent in 'queueA' but num = {$firstNum}\n";
} else {
    $channel->basic_publish($msg, '', 'queueB');
    echo " [x] Sent in 'queueB' but num = {$firstNum}\n";
}


$channel->close();
$connection->close();
