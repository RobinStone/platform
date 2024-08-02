<?php
use Workerman\Worker;
use Workerman\Timer;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../CONTROLLERS/config.php';
require_once __DIR__.'/../libs/class_DB.php';
require_once __DIR__.'/../libs/class_SUBD.php';
require_once __DIR__.'/../libs/class_VALUES.php';
require_once __DIR__.'/../libs/class_REDISE.php';
require_once __DIR__.'/../libs/class_Access.php';
require_once __DIR__.'/../libs/class_TELE.php';
require_once __DIR__.'/../APPLICATIONS/SHOPS/libs/class_CATALOGER.php';
require_once __DIR__.'/../APPLICATIONS/SHOPS/libs/class_SHOP.php';
require_once __DIR__.'/../libs/class_SEARCH.php';
require_once __DIR__.'/../libs/class_SORT.php';
require_once __DIR__.'/../libs/class_Message.php';
require_once __DIR__.'/../libs/class_PROFIL.php';
require_once __DIR__.'/../CONTROLLERS/soc_autoload.php';

$context = [
    'ssl' => [
        'local_cert' => '/var/www/httpd-cert/www-root/rumbra.ru_le3.crt',
        'local_pk' => '/var/www/httpd-cert/www-root/rumbra.ru_le3.key',
        'verify_peer' => false,
    ],
];

$wsWorker = new Worker('websocket://80.87.203.249:2347', $context);
$wsWorker->transport = 'ssl';
$wsWorker->count = 4;

$wsWorker->onConnect = function($connection) use ($wsWorker) {
    $connection->onMessage = function($connection, $data) use ($wsWorker) {
        $data = json_decode($data, JSON_UNESCAPED_UNICODE);
        if(isset($data['com'])) {
            switch ($data['com']) {
                case 'finder':
                    $data['RESULT'] = SEARCH::find($data['text'], -1, false, true);
                    break;
            }
            $connection->send(json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    };
    echo "Connection OPEN_SYS \n";
};

$wsWorker->onClose = function($connection) use ($wsWorker) {

    echo "Connection CLOSE_SYS \n";
};

$wsWorker->onWorkerStart = function($wsWorker) {
    Timer::add(60, function () use ($wsWorker) {
        try {
            $test_row = SUBD::getLineDB('users', 'login', 'robin');
            if (is_array($test_row)) {
                setParam('date_last_search', date('Y-m-d H:i:s'));
            } else {
                setParam('date_last_search', 'NOT FIND ROBIN');
            }
        } catch (Exception $e) {
            say('ERROR TIMER FINDER...');
            say($e);
            setParam('date_last_search', 'ERROR');
        }
    });
};
//$wsWorker->onWorkerStart = function($wsWorker) use ($red) {
//    Timer::add(10, function() use ($wsWorker, $red) {
//
//    });
//};

//$wsWorker->onMessage = function($connection, $data) use ($wsWorker, $red) {
//    global $clients;
//    $sha = $clients[$connection->id]['data']['SHA'];
//    $id = (int)$red->obj_get('SHA', $sha);
//    TELE::send_at_user_name('robin', $id);
//    foreach($wsWorker->connections as $clientConnection) {
//        $clientConnection->send($data);
//    }
//};

Worker::runAll();