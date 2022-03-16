<?php

//отправка fanout отправляет во все очереди так же настроено подтверждение получения и durable

//отправляет сообщениия во все очереди какие его слушают, их может быть много или может не быть сосвсем
//пример отправки
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_3_publish_subscribe/emit_log.php test send log

//так можно писать лог, лежит в корне
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_3_publish_subscribe/receive_logs.php > ./logs/log.log

//так просто слушать в любом количествке терминалов
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_3_publish_subscribe/receive_logs.php

//так можно посмотреть сколько очередей и логов активно в консоли раббита
//заходим в косноль
//docker exec -it rabbit-rabbitmq bash
//и пишем
//rabbitmqctl list_bindings

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

$channel->queue_bind($queue_name, 'logs');

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();