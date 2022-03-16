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
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS);
$channel = $connection->channel();

$channel->exchange_declare('topic_logs', 'topic', false, false, false);

$routing_key = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'anonymous.info';
$data = implode(' ', array_slice($argv, 2));
if (empty($data)) {
    $data = "Hello World!";
}

$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'topic_logs', $routing_key);

echo ' [x] Sent ', $routing_key, ':', $data, "\n";

$channel->close();
$connection->close();