<?php

/*
*    Этот файл принимает сообщения с 2 числами из файла RabbitMQ/mySimple_1/sending.php
*    слушает очередь queueA
*    проверяет обачисла на простоту, если хотя бы одно из чисел простое - ничего не делает,
*    иначе к первому числу прибавляется 1 и оба числа отправляются в queueB.
*
*    Работает так:
*    Запускаем в консоли докера чтоб отправить сообщение команду
*    `make my_sending_simple` и происходит отправка в одну из очередей
*
*    Чтоб получить сообщения из queueA запускаем
*    `make receive_A`
*/

require_once __DIR__ . '/../../vendor/autoload.php';
require_once  __DIR__ . '/../../functions/functions.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('queueA', false, false, false, false);
$channel->queue_declare('queueB', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) use ($channel) {

    $data = stringToArray($msg->body);
    $primeValueFirstNum = is_primeOrDivider ($data['firstNum']);
    $primeValueSecondNum = is_primeOrDivider ($data['secondNum']);

    if (is_prime((int) $data['firstNum'])) {
        echo " [Prime 1] First num is prime = {$data['firstNum']}, all data {$msg->body}, prime check {$data['firstNum']}, return = {$primeValueFirstNum}\n";
        echo " [Prime 1] Sending was not \n\n";
    } elseif (is_prime((int) $data['secondNum'])) {
        echo " [Prime 2] Second num is prime = {$data['secondNum']}, all data {$msg->body}, prime check {$data['secondNum']}, return = {$primeValueSecondNum}\n";
        echo " [Prime 2] Sending was not \n\n";
    } else {
        $oldFirstNum = $data['firstNum'];
        $data['firstNum'] += 1;
        $jsonData = json_encode($data);
        $newMsg = new AMQPMessage($jsonData);
        $channel->basic_publish($newMsg, '', 'queueB');
        echo " [Not prime] All num is not prime = {$msg->body}\n";
        echo " [Not prime] First num = {$oldFirstNum}, prime check {$oldFirstNum}, return = {$primeValueFirstNum}\n";
        echo " [Not prime] Second num = {$data['secondNum']}, prime check {$data['secondNum']}, return = {$primeValueSecondNum}\n";
        echo " [Not prime] Sent in 'queueB' new data {$newMsg->body}\n\n";
    }
};

$channel->basic_consume('queueA', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();