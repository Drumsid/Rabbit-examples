<?php

//пример работы 1 поставщика и нескольких консьюмеров.
//запускаем минимум 2 worker.php
//затем в new_task.php отправляем сразу несколько сообщений. Такого вида:
//php new_task.php First message.
//php new_task.php Second message..
//php new_task.php Third message...
//php new_task.php Fourth message....
//php new_task.php Fifth message.....
//и смотрим как сообщения уходят в разные консьюмеры




require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

//отмечаем в $channel->queue_declare 3 аргумент (durable) true чтоб не потерять сообщения если серевер крашнется
$channel->queue_declare('task_queue', false, true, false, false);

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = "Hello World!";
}
//в массиве добавляем сообщениям PERSISTENT для сохранения в момент краша. хотя это не 100% гарантия
$msg = new AMQPMessage(
    $data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
);

$channel->basic_publish($msg, '', 'task_queue');

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();