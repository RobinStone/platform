<?php

use JetBrains\PhpStorm\NoReturn;

class API {
    private string $api_controller;
    private $controller_instance;

    function __construct(string $api_controller) {
        $api_controller = ucfirst($api_controller);
        $this->api_controller = $api_controller;
        $controller_file = "./CONTROLLERS/API/" . $api_controller . ".php";
        if (file_exists($controller_file)) {
            require_once $controller_file;
            if (class_exists($api_controller)) {
                $this->controller_instance = new $api_controller();
            }
        } else {
            print_response_status(Response::STATUS_BAD_REQUEST);
        }
    }

    #[NoReturn] public function response($params=[]) {
        echo json_encode($params, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function getControllerInstance() {
        return $this->controller_instance;
    }

    public function __call($name, $arguments) {
        if (method_exists($this->controller_instance, $name)) {
            return call_user_func_array([$this->controller_instance, $name], $arguments);
        } else {
            return Response::STATUS_BAD_REQUEST;
        }
    }

    public static function INIT() {
        $server = $_SERVER;
        $PARAMS = [];
        if(isset($server['PHP_AUTH_USER'], $server['PHP_AUTH_PW'])) {
            $token_server = md5($server['PHP_AUTH_USER'].$server['PHP_AUTH_PW']);
            if($row = SQL_ONE_ROW(q("
                SELECT * FROM `messages` WHERE  
                `action`        = 'токен' AND 
                `actor`         = '".db_secur($server['PHP_AUTH_USER'])."' AND
                `target`        = '".$token_server."' AND
                `datatime`     >= '".date('Y-m-d H:i:s')."' LIMIT 1
                "))) {
            } else {
                print_response_status(Response::STATUS_UNAUTHORIZED);
            }
        } else {
            print_response_status(Response::STATUS_UNAUTHORIZED);
        }

        $api_request_array = explode("/", explode("?", ($server['REQUEST_URI'] ?? ""), 2)[0]);
        if(count($api_request_array) < 3) {
            print_response_status(Response::STATUS_BAD_REQUEST);
        }

        $method = '';

        switch($server['REQUEST_METHOD']) {
            case 'GET':
                $dt = explode('?', $server['REQUEST_URI']);
                if(count($dt) === 2) {
                    $dt = explode('&', $dt[1]);
                } else {
                    $dt = [];
                }
                $newData = [];
                foreach ($dt as $item) {
                    $parts = explode('=', $item);
                    $newData[$parts[0]] = $parts[1];
                }
                $PARAMS = [
                    'DATA'=>$newData
                ];
                $method = 'index';
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $rawData = file_get_contents('php://input');
                $data = json_decode($rawData, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $PARAMS = [
                        'DATA'=>$data
                    ];
                } else {
                    print_response_status(Response::STATUS_INTERNAL_SERVER_ERROR);
                }
                if($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $method = 'create';
                }
                if($_SERVER['REQUEST_METHOD'] === 'PUT') {
                    $method = 'update';
                }
                if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    $method = 'delete';
                }
                break;
        }

        if($method !== '') {
            $api = new API($api_request_array[2]);
            $PARAMS['PATH'] = $api_request_array;
            if (method_exists($api->getControllerInstance(), $method)) {
                switch($method) {
                    case 'index': $api->index($PARAMS); break;
                    case 'create': $api->create($PARAMS); break;
                    case 'update': $api->update($PARAMS); break;
                    case 'delete': $api->delete($PARAMS); break;
                }
            } else {
                print_response_status(Response::STATUS_METHOD_NOT_ALLOWED);
            }
        }
    }

}