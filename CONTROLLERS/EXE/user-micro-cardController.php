<?php
$us = SUBD::getLineDB('users', 'login', $login);
if(is_array($us)) {
    $P = PROFIL::create($login, PROFIL_TYPE::login);
} else {
    Message::addError('Неудалось найти пользователя создавшего товар...');
    header('Location: /404');
}

$saler_name = $P->get('name');

if($saler_name === '') {
    $saler_name = 'НЕТ ИМЕНИ';
}

$img = $P->get_field('avatar');
if($img === '-' || $img === '' || !file_exists('./IMG/img100x100/'.$img)) {
    $img = '/DOWNLOAD/20230531-120816_id-2-498113.svg';
} else {
    $img = '/IMG/img100x100/'.$img;
}

$P->set('avatar', $img);
$P->set('saler_name', $saler_name);

$data_reg = $P->get_field('dataReg');
if($data_reg === '-' || $data_reg === '') {
    $data_reg = date('d.m.Y');
} else {
    $data_reg = date('d.m.Y', strtotime($data_reg));
}

$score = round((float)$P->get('score'), 2);
$reviews = 0;

$id_write_card = (int)$P->get_field('id', -1);
$subs = SQL_ONE_ROW(q("SELECT * FROM `subscriptions` WHERE `self_id`=".Access::userID()." AND `subscr_id`=".$id_write_card));

$writer_text = "Подписаться";
if(is_array($subs)) {
    $writer_text = "Отписаться";
}

$id = (int)$P->get_field('id');

$rows = SQL_ROWS(q("SELECT stars FROM reviews_prod WHERE owner_product=".$id));
$stars = 0;

foreach($rows as $v) {
    $stars += (float)$v['stars'];
}

$reviews = count($rows);
if($reviews > 0) {
    $stars = round($stars / $reviews, 2);
}

$subs_count = SQL_ONE_ROW(q("SELECT COUNT(*) FROM subscriptions WHERE subscr_id = ".$P->get_field('id')))['COUNT(*)'];

if(empty($my_subscr_count)) {
    $my_subscr_count = SQL_ONE_ROW(q("SELECT COUNT(*) FROM subscriptions WHERE self_id = ".$P->get_field('id')))['COUNT(*)'];
}