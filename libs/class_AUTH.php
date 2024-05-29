<?php
class AUTH {
    /**
     * ПРОВЕРЯЕТ НАЛИЧИЕ ТОКЕНА И ЕСЛИ ТОКЕН ПОДТВЕРЖДЁН, АВТОРИЗУЕТ ПОЛЬЗОВАТЕЛЯ
     * @return void
     */
    public static function token_verification_and_auth() {
        if(isset($_GET['token_auth'])) {
            if($row = SQL_ONE_ROW(q("SELECT * FROM messages WHERE target='".db_secur($_GET['token_auth'])."' AND action = 'разрешение' LIMIT 1"))) {
                PROFIL::AUTH_LOGIN((int)$row['actor']);
                Message::addMessage('Авторизация - устпешна');
            }
        }
    }
}