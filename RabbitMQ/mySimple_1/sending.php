<?php

/*
*    Этот файл отправляет сообщения из 2 чисел в две очереди queueA и queueB
*    в  queueA будет отправка если 1 число не четное
*    иначе отправка в queueB
*    receiveA.php принимает сообщение из очереди queueA.
*    receiveB.php принимает сообщение из очереди queueB.
*
*    Работает так:
*    Запускаем в консоли докера чтоб отправить сообщение команду
*    `make my_sending_simple` и происходит отправка в одну из очередей
*
*    Чтоб получить сообщения из queueA запускаем
*    `make receive_A`
*
*    Чтоб получить сообщения из queueB запускаем
*    `make receive_B`
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
    echo " [x] Sent in 'queueA' but num = {$firstNum} is odd\n";
} else {
    $channel->basic_publish($msg, '', 'queueB');
    echo " [x] Sent in 'queueB' but num = {$firstNum} is even\n";
}


$channel->close();
$connection->close();
