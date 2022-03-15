<?php

/*
*    Этот файл отправляет сообщения из 2 чисел в две очереди Queue_A и Queue_B
*    в  Queue_A будет отправка если 1 число не четное
*    иначе отправка в Queue_B
*    receiveA.php принимает сообщение из очереди Queue_A.
*    receiveB.php принимает сообщение из очереди Queue_B.
*
*    Работает так:
*    Запускаем в консоли докера чтоб отправить сообщение команду
*    `make my_sending_simple` и происходит отправка в одну из очередей
*
*    Чтоб получить сообщения из Queue_A запускаем
*    `make receive_A`
*
*    Чтоб получить сообщения из Queue_B запускаем
*    `make receive_B`
*/

require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const MIN_INTEGER = 1;
const MAX_INTEGER = 10000;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

//отмечаем в $channel->queue_declare 3 аргумент (durable) true чтоб не потерять сообщения если серевер крашнется
$channel->queue_declare('Queue_A', false, true, false, false);
$channel->queue_declare('Queue_B', false, true, false, false);

$firstNum = rand(MIN_INTEGER, MAX_INTEGER);
$secondNum = rand(MIN_INTEGER, MAX_INTEGER);

$arrNums = ['firstNum' => $firstNum, 'secondNum' => $secondNum];
$jsonData = json_encode($arrNums);

$msg = new AMQPMessage($jsonData, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
if ($firstNum % 2 == 1) {
    $channel->basic_publish($msg, '', 'Queue_A');
    echo " [x] Sent in 'Queue_A' but num = {$firstNum} is odd\n";
} else {
    $channel->basic_publish($msg, '', 'Queue_B');
    echo " [x] Sent in 'Queue_B' but num = {$firstNum} is even\n";
}


$channel->close();
$connection->close();
