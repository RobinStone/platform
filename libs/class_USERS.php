<?php
class USERS {
    public static array $errors = [];

    public static function create(string $login, int $level, string $password, MODALITY $modality=MODALITY::empty, string $phone='-', string $tele='-', string $email='-'): bool|int
    {
        if($level < 0) { $level = 0; }
        if($level > 7) { $level = 7; }
        if(!q("
        INSERT INTO users SET
        `login`         = '".db_secur($login)."',
        `status`        = 1,
        `level`         = '".$level."',
        `modality`      = '".$modality->value."',  
        `password`      = '".crypter($password)."',   
        `phone`         = '".db_secur($phone)."',   
        `tele`          = '".db_secur($tele)."',   
        `email`         = '".db_secur($email)."',
        `params`        = 'start=".date('Y-m-d H:i:s')."'
        ")) {
            Message::addError('Не удалось создать пользователя...');
            self::$errors[] = 'Не удалось создать пользователя...';
            return false;
        }
        $id = SUBD::get_last_id();
        $P = new PROFIL($id);
        $P->add_alert(ALERT_TYPE::WARNING, ['text'=>'Необходимо сменить имя, для того, что бы вы могли размещать объявления на этом сайте.', 'link'=>'/profil?title=account'], 'change_name');
        $P->add_alert(ALERT_TYPE::WARNING, ['text'=>'Необходимо сменить пароль, для того, что бы в следующий раз вы могли бы войти в свой аккаунт. Сейчас ваш пароль - это код, который вы получили в СМС', 'link'=>'/profil?title=account'], 'change_pass');
        t('created - '.$id);
        return $id;
    }

    public static function delete(int $id_user): bool
    {
        if($id_user === Access::userID()) {
            self::$errors[] = 'Запрещено удалять собственного пользователя.';
            return false;
        }
        if(Access::get_access($id_user, 'access_to_complite_remoove') === false) {
            self::$errors[] = 'Нет санкции "access_to_complite_remoove" для пользователя с ID='.$id_user;
            return false;
        }
        INCLUDE_CLASS('SHOPS', 'SHOP');
        $count = 0;
        $shops = SHOP::get_all_my_shops($id_user);
        foreach($shops as $k=>$v) {
            SHOP::delete_shop_complite($v['id'], true);
            ++$count;
        }
        q("DELETE FROM `users` WHERE `id`=".$id_user);
        return true;
    }

    public static function isset_user_login(string $login): bool|array
    {
        if($row = SQL_ONE_ROW(q("SELECT `id` FROM `users` WHERE `login`='".db_secur($login)."' LIMIT 1"))) {
            return $row;
        }
        return false;
    }
}