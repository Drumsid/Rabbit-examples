<?php

//отправляем сообщение с ключом. Ключ, первое слово в сообщении пример отправки с ключом error
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_4_routing/emit_log_direct.php error send msg


//вот так будет слушать сообщения с ключами warning и error и писать в файл
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_4_routing/receive_logs_direct.php warning error > ./logs/routing.log

//вот так будет слушать сообщения с ключами info warning error и выводить в консоль
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_4_routing/receive_logs_direct.php info warning error

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

$channel->exchange_declare('direct_logs', 'direct', false, false, false);

$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';

$data = implode(' ', array_slice($argv, 2));
if (empty($data)) {
    $data = "Hello World!";
}

$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'direct_logs', $severity);

echo ' [x] Sent ', $severity, ':', $data, "\n";

$channel->close();
$connection->close();