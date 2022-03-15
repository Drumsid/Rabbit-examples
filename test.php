<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once  __DIR__ . '/functions/functions.php';
require_once __DIR__ . '/Db/connectDb.php';

//use PhpAmqpLib\Connection\AMQPStreamConnection;
//
//$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
//$channel = $connection->channel();
//
////отмечаем в $channel->queue_declare 3 аргумент (durable) true чтоб не потерять сообщения если серевер крашнется
//$channel->queue_declare('Queue_C', false, true, false, false);
//
//$callback = function ($msg)  {
////    header("Refresh: 0");
//    $msg->ack();
//};

//раскомментируй руфвук чтоб страница обновлялась
//header("Refresh: 5");

$data = $pdo->query("SELECT * FROM queue")->fetchAll();

$reverse = array_reverse($data);
?>
    <ul>
        <?php
        foreach ($reverse as $row) {
            ?>
            <li>"Num:  <?= $row['value']?>, <?= $row['queue_name']?>"</li>
            <?php
        }
        ?>
    </ul>
<?php

////$channel->basic_qos = не отправлять новое сообщение рабочему процессу, пока он не обработает и не подтвердит предыдущее.
//$channel->basic_qos(null, 1, null);
////no_ack ставим flase чтоб работало подтверждение
//$channel->basic_consume('Queue_C', '', false, false, false, false, $callback);
//
//
//while ($channel->is_open()) {
//    $channel->wait();
//}
//
//$channel->close();
//$connection->close();