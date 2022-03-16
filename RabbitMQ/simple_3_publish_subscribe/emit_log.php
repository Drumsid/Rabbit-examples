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
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = "info: Hello World!";
}
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'logs');

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();