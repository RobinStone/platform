<?php

use JetBrains\PhpStorm\ArrayShape;

class Chat {
    public function index($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        wtf($params);

        $status = Response::STATUS_OK;

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }

    public function create($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        $data['method'] = 'CREATE';

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

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }
}