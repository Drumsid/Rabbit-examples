<?php

/*
*    Первый пример с офф сайта. Этот файл получает сообщения
*    sending.php принимает сообщение.
*    Работает так:
*    Запускаем в консоли докера чтоб отправить сообщение команду
*    `make sending_s_1`
*    Чтоб получить сообщения запускаем
*    `make receive_s_1`
*/

require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();