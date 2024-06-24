<?php

use JetBrains\PhpStorm\ArrayShape;

class Chat {
    public function index($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

//        wtf($params);

        if(!empty($params['PATH'][3])) {
            $login = $params['DATA']['login'] ?? '';
            $chat_name = $params['PATH'][4] ?? '';
            switch($params['PATH'][3]) {
                case 'logining':
                    if(!empty($login) && !empty($chat_name)) {
                        if($row = SQL_ONE_ROW(q("SELECT `id` FROM `users` WHERE `login` = '".db_secur($chat_name."_".$login)."' LIMIT 1"))) {
                            PROFIL::AUTH_LOGIN($row['id']);
                            $data['LINK'] = Access::create_access_link('speaker', $row['id']);
                            $data['LINK'] .= "&name=".$chat_name;
                            $status = Response::STATUS_OK;
                        } else {
                            $status = Response::STATUS_UNAUTHORIZED;
                            $data = "Not finded you account for chat.";
                        }
                    } else {
                        $status = Response::STATUS_NOT_FOUND;
                        $data = "Not sended chat name or user name.";
                    }
                    break;
            }
        }

        print_response([
            'status' => $status,
            'data' => $data
        ]);
    }

    public function create($params): array
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];

        if(!empty($params['DATA']['chat']) && !empty($params['DATA']['login'])) {
            $chat = $params['DATA']['chat'];
            $login = $params['DATA']['login'];

            if($sys_mess_row = SQL_ONE_ROW(q("SELECT `params`, `id` FROM `messages` WHERE `action`='токен' AND `actor`='".db_secur($chat)."' LIMIT 1"))) {
                if(!USERS::isset_user_login($chat."_".$login)) {
                    $params__ = unserialize($sys_mess_row['params']);
                    $limit = (int)get_attachment_value($params__, 'limit', 0);
                    if($limit > 0) {
                        if($id = USERS::create($chat."_".$login, 1, crypt($chat."_".$login, time()), MODALITY::empty, '-', '-', '-')) {
                            --$limit;
                            $params__ = serialize(set_attachment_value($params__, 'limit', $limit));
                            q("UPDATE `messages` SET `params`='".$params__."' WHERE `id`=".(int)$sys_mess_row['id']);
                            $status = Response::STATUS_CREATED;
                            $data = $id;
                        } else {
                            $status = Response::STATUS_NOT_MODIFIED;
                            $data = "Not created new user.";
                        }
                    } else {
                        $status = Response::STATUS_CONFLICT;
                        $data = "Limit connected for this token is low.";
                    }
                } else {
                    $status = Response::STATUS_CONFLICT;
                    $data = "User with this login - has been registred.";
                }
            } else {
                $status = Response::STATUS_NOT_FOUND;
                $data = "Chat name for this token - not found.";
            }
        }

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