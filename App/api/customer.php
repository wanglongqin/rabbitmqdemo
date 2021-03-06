<?php
namespace app\index\controller;

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;


set_time_limit(300);
try{
    $config = [
        'host'     => '192.168.0.180',
        'port'     => '5672',
        'user'     => 'root',
        'password' => 'admin',
        'vhost'    => '/'
    ];
    //$a = process_message(111);die();
    $queue_name = 'ffffffffffffa';

    $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'],$config['password'], $config['vhost']);
    $channel = $connection->channel();
    /**get 方式 一条一条获取结束**/
    $callback = function($message) {
        echo "\n--------\n";
        echo $message->body;
        echo "\n--------\n";

        $message->ack();

        // Send a message with the string "quit" to cancel the consumer.
        if ($message->body === 'quit') {
            $message->getChannel()->basic_cancel($message->getConsumerTag());
        }
    };

    //在接收消息的时候调用$callback函数
    $channel->basic_consume($queue_name, 'consumer' . rand(111111,99999999999), false, false, false, false, $callback);


    /**
     * @param \PhpAmqpLib\Channel\AMQPChannel $channel
     * @param \PhpAmqpLib\Connection\AbstractConnection $connection
     */
//        $shutdown = function($channel, $connection)
//        {
//            $channel->close();
//            $connection->close();
//        };
//
//        register_shutdown_function($shutdown, $channel, $connection);
    while ($channel->is_consuming()) {
        $channel->wait();
    }
    $channel->close();
    $connection->close();
} catch (\Exception $ex) {
    var_dump($ex);die();
}
die();