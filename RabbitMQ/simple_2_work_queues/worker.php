<?php

//пример работы 1 поставщика и нескольких консьюмеров. так же настроено подтверждение получения и durable
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

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

//отмечаем в $channel->queue_declare 3 аргумент (durable) true чтоб не потерять сообщения если серевер крашнется
$channel->queue_declare('task_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
//    отправка подтверждение получение ack = acknowledgment
//    в $channel->basic_consume, 4 аргумент так же должен быть false чтоб подтверждение работало
//    и теперь если в момент чтения сообщения консьюмер упадет и не доделает. Сообщение не потеряется
    $msg->ack();
};

//$channel->basic_qos = не отправлять новое сообщение рабочему процессу, пока он не обработает и не подтвердит предыдущее.
$channel->basic_qos(null, 1, null);
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();