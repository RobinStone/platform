<?php$data = file_get_contents('php://input');$data = json_decode($data, true);//say($data);if(isset($data['event'], $data['object']['id']) && $data['event'] === 'payment.succeeded') {    $code = md5($data['object']['metadata']['user_id'].$data['object']['metadata']['id_pay'].Core::$CRYPTER_SALT_1);    $row = SUBD::getLineDB('pays', 'code', $code);    if(is_array($row)) {        if(round((float)$row['summ'], 2) === round((float)$data['object']['amount']['value'], 2)) {            $user_id = (int)$data['object']['metadata']['user_id'];            $pay_id = (int)$data['object']['metadata']['id_pay'];            if((int)$row['status'] !== 1) {                $P = new PROFIL($user_id);                $cash = (float) $P->get('cash', '0');                $cash = round($cash + (float) $row['summ'], 2);                $P->set('cash', $cash);                q("                UPDATE `pays` SET                 `time_pay_ok`='" . date('Y-m-d H:i:s') . "',                 `status`=1,                `code`='".db_secur($data['object']['id'])."'                WHERE `id`=" . (int) $row['id']);                t("Оплатил [" . $P->get_field('login') . "], summ=" . $row['summ']);            } else {                t('Попытка повторного зачисления по реквизитам...');            }        } else {            t('Несовпадение по сумме');        }    } else {        t('Ошибка кода верификации');    }}//'code'=>md5($idempotenceKey.$user_id.$id_pay),t('q_cass - ok answer from q-cass');