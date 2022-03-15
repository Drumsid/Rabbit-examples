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

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

$channel->exchange_declare('direct_logs', 'direct', false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

$severities = array_slice($argv, 1);
if (empty($severities)) {
    file_put_contents('php://stderr', "Usage: $argv[0] [info] [warning] [error]\n");
    exit(1);
}

foreach ($severities as $severity) {
    $channel->queue_bind($queue_name, 'direct_logs', $severity);
}

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();