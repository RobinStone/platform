<?php

use JetBrains\PhpStorm\ArrayShape;

class Test {
    public function index($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }

    public function create($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }

    public function update($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }

    public function delete($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }
}