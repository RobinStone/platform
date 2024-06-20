<?php

use JetBrains\PhpStorm\ArrayShape;

class Chat {
    public function index($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        $data['PARAMS'] = $params;
        $data['method'] = 'INDEX';

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }

    public function create($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

//        if(isset($params['DATA']['user_id'], $params['DATA']['login'])) {
//            if($row = SQL_ONE_ROW(q("
//            SELECT `id` FROM `users` WHERE
//            `id`=".(int)$params['DATA']['user_id']." AND
//            `login`='".db_secur($params['DATA']['login'])."'
//            "))) {
//                $id_user = (int)$row['id'];
//                PROFIL::AUTH_LOGIN($id_user);
//                $data['LINK'] = Access::create_access_link('chat', $id_user);
//            }
//        }

        $data['PARAMS'] = $params;
        $data['method'] = 'POST';

        if(!empty($data)) {
            $status = Response::STATUS_OK;
        }

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }

    public function update($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        $data['method'] = 'UPDATE';
        $data['PARAMS'] = $params;

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }

    public function delete($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        $data['method'] = 'DELETE';
        $data['PARAMS'] = $params;

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }
}