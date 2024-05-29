<?php
class SMSaero {
    public static function send($number, $text): bool
    {
        $smsaero_api = new SmsaeroApiV2('Kamranltd@yandex.ru', 'WB7RmbKlTu2VJq6GbpD6e9LIbfWf', 'LABUTON'); // api_key из личного кабинета
        $number = str_replace('(', '', $number);
        $number = str_replace(')', '', $number);
        $number = str_replace('-', '', $number);
        $number = str_replace('+', '', $number);
        $number = str_replace(' ', '', $number);
        $txt = $smsaero_api->send($number, $text); // Отправка сообщений
        if(is_array($txt) && $txt['success'] == '1') {
            return true;
        } else {
            TELE::send_at_user_name('robin', 'Отправка - не удолась! Смотри в SAY...');
            say($txt);
            return false;
        }
    }
}