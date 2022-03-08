<?php

/*
*    Этот файл принимает сообщения с 2 числами из файла RabbitMQ/mySimple_1/sending.php
*    слушает очередь queueB
*    проверяет первое число на четность(бросает исключение в противном случае), перемножает присланные числа,
*    складывает цифры полученного числа в сумму и пишет ее в БД
*
*    Работает так:
*    Запускаем в консоли докера чтоб отправить сообщение команду
*    `make my_sending_simple` и происходит отправка в одну из очередей
*
*    Чтоб получить сообщения из queueB запускаем
*    `make receive_B`
*/

require_once __DIR__ . '/../../vendor/autoload.php';
require_once  __DIR__ . '/../../functions/functions.php';
require_once __DIR__ . '/../../Db/connectDb.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('queueB', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) use ($pdo) {

    $data = stringToArray($msg->body);

    if ((int) $data['firstNum'] % 2 == 0) {
        $value = countData($data);
        insertToDb($pdo, $value, 'queueB');
        echo " [Insert] Get data {$msg->body}, Insert to DB {$value}\n\n";
    } else {
        throw new Exception(" [Exception] First number is odd {$msg->body}\n\n");
//        echo " [Exception] First number is odd {$msg->body}\n\n";
    }
};

$channel->basic_consume('queueB', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();