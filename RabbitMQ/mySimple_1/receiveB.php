<?php

/*
*    Этот файл принимает сообщения с 2 числами из файла RabbitMQ/mySimple_1/sending.php
*    слушает очередь Queue_B
*    проверяет первое число на четность(бросает исключение в противном случае), перемножает присланные числа,
*    складывает цифры полученного числа в сумму и пишет ее в БД
*
*    Работает так:
*    Запускаем в консоли докера чтоб отправить сообщение команду
*    `make my_sending_simple` и происходит отправка в одну из очередей
*
*    Чтоб получить сообщения из Queue_B запускаем
*    `make receive_B`
*/

require_once __DIR__ . '/../../vendor/autoload.php';
require_once  __DIR__ . '/../../functions/functions.php';
require_once __DIR__ . '/../../Db/connectDb.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

//отмечаем в $channel->queue_declare 3 аргумент (durable) true чтоб не потерять сообщения если серевер крашнется
$channel->queue_declare('Queue_B', false, true, false, false);

echo " [*] Queue_B waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) use ($pdo) {

    $data = stringToArray($msg->body);

    if ((int) $data['firstNum'] % 2 == 0) {
        $value = countData($data);
        insertToDb($pdo, $value, 'Queue_B');
        echo " [Insert] Get data {$msg->body}, Insert to DB {$value}\n\n";
        $msg->ack();
    } else {
        throw new Exception(" [Exception] First number is odd {$msg->body}\n\n");
//        echo " [Exception] First number is odd {$msg->body}\n\n";
    }
};

//$channel->basic_qos = не отправлять новое сообщение рабочему процессу, пока он не обработает и не подтвердит предыдущее.
$channel->basic_qos(null, 1, null);
//no_ack ставим flase чтоб работало подтверждение
$channel->basic_consume('Queue_B', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();