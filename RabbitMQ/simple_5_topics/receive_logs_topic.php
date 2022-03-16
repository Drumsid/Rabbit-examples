<?php
//получение сообщений по ключам. которые отправляються строкой и разделяются точками
//пример "key1.key2" * может заменить ровно одно слово и будет похоже на поведение direct, # может заменить ноль или
// более слов и будет похоже на поведение fanout

//пример получения всех сообщений
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_5_topics/receive_logs_topic.php "#"

//пример получения по 1 ключу = kern
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_5_topics/receive_logs_topic.php "kern.*"

//пример получения по 2 ключу = critical
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_5_topics/receive_logs_topic.php "*.critical"

//пример получения по kern и critical в 1 очередь
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_5_topics/receive_logs_topic.php "kern.*" "*.critical"

//пример отправки, попадет в очередь которая слушает "*.critical", "#" и "kern.*" "*.critical"
//docker exec -it rabbit-php-fpm  php RabbitMQ/simple_5_topics/emit_log_topic.php "test.critical" "A critical kernel error"


require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

$channel->exchange_declare('topic_logs', 'topic', false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

$binding_keys = array_slice($argv, 1);
if (empty($binding_keys)) {
    file_put_contents('php://stderr', "Usage: $argv[0] [binding_key]\n");
    exit(1);
}

foreach ($binding_keys as $binding_key) {
    $channel->queue_bind($queue_name, 'topic_logs', $binding_key);
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