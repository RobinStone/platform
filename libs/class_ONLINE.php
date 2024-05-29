<?php
use JetBrains\PhpStorm\ArrayShape;
class ONLINE {
    public static array $all = [];       // ТУТ [connection_id] = token
    public static array $tokens = [];    // ТУТ [token] = connection_id
    public static array $users = [];     // ТУТ [token] = [NAME: userName, ID: id_user, IMG: photouser]
                                         // ЭТИ переменные есть только когда кто то подключается к чату
    public static array $users_ids = []; // ТУТ [id_user][] = token


    public static function token_TO_connection_id(string $token):array|false {
        return self::$tokens[$token] ?? false;
    }
    public static function connection_id_TO_token(int $connection_id) {
        return self::$all[$connection_id] ?? false;
    }
    public static function token_TO_user_params(string $token) {
        return self::$users[$token] ?? false;
    }
    public static function id_user_TO_tokens(int $id_user):array|false {
        return self::$users_ids[$id_user] ?? false;
    }

    /**
     * Регистрирует любого пользователя, который зашёл на сайт,
     * далее - если работает чат (даже в скрытом режиме) происходит поддержка сессии
     * @return void
     */
    public static function INIT() {
        SITE::$ip = Access::scanIP();
        try {
            SITE::$place = GEO2IP::IP2STRING(SITE::$ip);
        } catch (Exception $e) {
            SITE::$place = 'Не определён';
        }
        $auth = 0;
        if(SITE::$user_id !== -1) {
            $auth = 1;
        }
        if(!$row = SQL_ONE_ROW(q("SELECT * FROM `online` WHERE `token`='".Core::$SESSIONCODE."'"))) {
            start_transaction();
            q("
            INSERT INTO `online` SET
            `connection_id`='',
            `token`='".Core::$SESSIONCODE."',
            `logined`=".$auth.",
            `user_auth`=".SITE::$user_id.",
            `started`='".SITE::$dt."',
            `last_action`='".SITE::$dt."',
            `place`='".SITE::$place."',
            `ip`='".SITE::$ip."'
            ");
            if(Access::userID() !== -1) {
                q("
                UPDATE hu_in_room SET token='" . Core::$SESSIONCODE . "' WHERE 
                id_user=" . Access::userID() . "
                ");
            }
            end_transaction();
        } else {
            q("
            UPDATE `online` SET 
            `last_action`='".SITE::$dt."',
            `logined`=".$auth.",
            `user_auth`=".SITE::$user_id.",
            `token`='".Core::$SESSIONCODE."',     
            `ip`='".SITE::$ip."',
            `place`='".SITE::$place."'
            WHERE `id`=".(int)$row['id']);
        }
    }

    public static function close_session(int $id_connection) {
        self::offline_id_client($id_connection);
    }

    public static function get_user_for_token(string $token): bool|array
    {
        if($row = SQL_ONE_ROW(q("SELECT * FROM `online` WHERE `token`='".db_secur($token)."'"))) {
            return $row;
        }
        return false;
    }

    public static function update_online_connections(): array {
        start_transaction();
        $rows = SQL_ROWS_FIELD(q("SELECT connection_id, token FROM `online`"), 'connection_id');
        foreach($rows as $k=>$v) {
            $rows[$k] = $v['token'];
        }
        self::$all = $rows;
        q("UPDATE `online` SET `last_action`='".date('Y-m-d H:i:s')."' WHERE `connection_id`<>''");
        end_transaction();
        return $rows;
    }

    /**
     * СОЗДАЁТ СВЯЗЬ ДЛЯ ТАБЛИЦЫ `online` МЕЖДУ id_connection и token
     * а так же регистрирует в массиве эту связь в $all [ 'id_connection' ] = token
     * @param $token
     * @param $id_connect
     * @param int $user_id
     * @return array
     */
    #[ArrayShape(['connection_id' => "", 'token' => "", 'user' => "array|mixed", 'registred' => false])]
    public static function set_connection_id($token, $id_connect, int $user_id=-1):array {

        self::$all[$id_connect] = $token;
        self::$tokens[$token][] = $id_connect;

        q("UPDATE `online` SET `connection_id`='".implode(' ', self::$tokens[$token])."' WHERE `token`='".db_secur($token)."' LIMIT 1");
        $registred = false;
        if($user_id <> -1) {
            $P = PROFIL::create($user_id, PROFIL_TYPE::id);
            self::$users[$token] = [
                'NAME'=>$P->get('name', $P->get_field('login')),
                'ID'=>$user_id,
                'IMG'=>$P->get_field('logo', 'none.svg'),
            ];
            $registred = true;
            self::$users_ids[$user_id][$id_connect] = $token;
        } else {
            self::$users[$token] = [
                'NAME'=>'GUEST-'.mb_substr($token, 0, 4),
                'ID'=>-1,
                'IMG'=>'20240109-175521_id-2-663228.svg',
            ];
        }

        return [
            'connection_id'=>$id_connect,
            'token'=>$token,
            'user'=>self::$users[$token],
            'registred'=>$registred,
        ];
    }

    public static function get_user_from_token($token) {
        if($row = SQL_ONE_ROW(q("SELECT * FROM `online` WHERE `token`='".db_secur($token)."' LIMIT 1"))) {
            return PROFIL::get_user((int)$row['user_auth']);
        }
        return false;
    }

    public static function send_in_SOC(int $id_user, string $txt) {
        if($row = SQL_ONE_ROW(q("SELECT * FROM users WHERE id=".$id_user))) {
            $P = PROFIL::init($id_user);
            say($P);
        }
    }

    public static function clear_old() {
        q("DELETE FROM `online` WHERE `last_action`<'".VALUES::plus_minutes(-2)."' ");

        $rows = SQL_ROWS(q("SELECT id FROM room WHERE last_use < '".VALUES::plus_minutes(-5)."' AND static=0 "));
        if(count($rows) > 0) {
            $rows = array_column($rows, 'id');
            foreach($rows as $v) {

            }
            q("DELETE FROM `room` WHERE id IN (".implode(',', $rows).") ");
            q("DELETE FROM `hu_in_room` WHERE id_room IN (".implode(',', $rows).") ");
            q("DELETE FROM `main_chat` WHERE id_room IN (".implode(',', $rows).") ");
            t('ОЧИЩЕНО '.count($rows).' комнат');
        }
    }

    public static function update_local_token_chat() {
        self::INIT();
    }

    public static function access($token):bool {
        if(isset(self::$tokens[$token])) {
            return true;
        }
        return false;
    }

    /**
     * Очищает память класса ONLINE
     * @param int $id_connect
     * @return void
     */
    public static function offline_id_client(int $id_connect) {
        if(isset(self::$all[$id_connect])) {
            $token = self::$all[$id_connect];
            $id_user = self::$users[$token]['ID'] ?? -1;
//
//            t('TOKEN=['.$token.']');
//            t('ID-USER = '.$id_user);
            if($id_user !== -1 && isset(self::$users_ids[$id_user][$id_connect])) {
                unset(self::$users_ids[$id_user][$id_connect]);
                if(count(self::$users_ids[$id_user]) === 0) {
                    unset(self::$users_ids[$id_user]);
                }
            }

            unset(self::$tokens[$token][array_search($id_connect, self::$tokens[$token])]);

            q("UPDATE `online` SET `connection_id`='".implode(' ', self::$tokens[$token])."' WHERE `token`='".$token."' LIMIT 1");

            if(count(self::$tokens[$token]) === 0) {
                unset(self::$tokens[$token]);
            }

            if(!isset(self::$tokens[$token])) {
                unset(self::$users[$token]);
            }

            unset(self::$all[$id_connect]);
        }
    }

}