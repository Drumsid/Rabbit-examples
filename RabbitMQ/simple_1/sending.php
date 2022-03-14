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

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();


$channel->queue_declare('hello', false, false, false, false);

//for ($i = 1; $i <= 1000000; $i++) {
    $key = rand();
    $textMessage = "Hello World_{$key}";

    $msg = new AMQPMessage($textMessage);
    $channel->basic_publish($msg, '', 'hello');

    echo " [x] Sent '{$textMessage}'\n";
//}


$channel->close();
$connection->close();
