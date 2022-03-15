<?php

/*
*    Этот файл принимает сообщения с 2 числами из файла RabbitMQ/mySimple_1/sending.php
*    слушает очередь Queue_A
*    проверяет обачисла на простоту, если хотя бы одно из чисел простое - ничего не делает,
*    иначе к первому числу прибавляется 1 и оба числа отправляются в Queue_B.
*
*    Работает так:
*    Запускаем в консоли докера чтоб отправить сообщение команду
*    `make my_sending_simple` и происходит отправка в одну из очередей
*
*    Чтоб получить сообщения из Queue_A запускаем
*    `make receive_A`
*/

require_once __DIR__ . '/../../vendor/autoload.php';
require_once  __DIR__ . '/../../functions/functions.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

//отмечаем в $channel->queue_declare 3 аргумент (durable) true чтоб не потерять сообщения если серевер крашнется
$channel->queue_declare('Queue_A', false, true, false, false);
$channel->queue_declare('Queue_B', false, true, false, false);

echo " [*] Queue_A waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) use ($channel) {

    $data = stringToArray($msg->body);
    $primeValueFirstNum = is_primeOrDivider ($data['firstNum']);
    $primeValueSecondNum = is_primeOrDivider ($data['secondNum']);

    if (is_prime((int) $data['firstNum'])) {
        echo " [Prime 1] First num is prime = {$data['firstNum']}, all data {$msg->body}, prime check {$data['firstNum']}, return = {$primeValueFirstNum}\n";
        echo " [Prime 1] Sending was not \n\n";
        //    отправка подтверждение получение ack = acknowledgment
        //    в $channel->basic_consume, 4 аргумент так же должен быть false чтоб подтверждение работало
        //    и теперь если в момент чтения сообщения консьюмер упадет и не доделает. Сообщение не потеряется
        $msg->ack();
    } elseif (is_prime((int) $data['secondNum'])) {
        echo " [Prime 2] Second num is prime = {$data['secondNum']}, all data {$msg->body}, prime check {$data['secondNum']}, return = {$primeValueSecondNum}\n";
        echo " [Prime 2] Sending was not \n\n";
        $msg->ack();
    } else {
        $oldFirstNum = $data['firstNum'];
        $data['firstNum'] += 1;
        $jsonData = json_encode($data);
        $newMsg = new AMQPMessage($jsonData, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($newMsg, '', 'Queue_B');
        echo " [Not prime] All num is not prime = {$msg->body}\n";
        echo " [Not prime] First num = {$oldFirstNum}, prime check {$oldFirstNum}, return = {$primeValueFirstNum}\n";
        echo " [Not prime] Second num = {$data['secondNum']}, prime check {$data['secondNum']}, return = {$primeValueSecondNum}\n";
        echo " [Not prime] Sent in 'Queue_B' new data {$newMsg->body}\n\n";
        $msg->ack();
    }
};

//$channel->basic_qos = не отправлять новое сообщение рабочему процессу, пока он не обработает и не подтвердит предыдущее.
$channel->basic_qos(null, 1, null);
//no_ack ставим flase чтоб работало подтверждение
$channel->basic_consume('Queue_A', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();