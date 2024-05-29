<?php
class IM {
    public int $id_connect=-1;
    public string $token='';
    public int $status=0;
    public int $id_user=-1;

    public static function INIT($token): IM|bool
    {
        if($row = SQL_ONE_ROW(q("
                    SELECT connection_id, token, logined, user_auth FROM online 
                    WHERE token='".db_secur($token)."' LIMIT 1
                    "))) {
            $my_im = new IM();
            $my_im->id_connect = (int)$row['connection_id'];
            $my_im->token = $row['token'];
            $my_im->status = (int)$row['logined'];
            $my_im->id_user = (int)$row['user_auth'];
            return $my_im;
        } else {
            return false;
        }
    }
}