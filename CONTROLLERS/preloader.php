<?php$JSVARS = SQL_ROWS_FIELD(q("SELECT * FROM `main` WHERE `js` = 1"), 'param');$arrs = SQL_ROWS_FIELD(q("SELECT * FROM `main` WHERE `php` = 1"), 'param');$PHP_VARS = [];foreach($arrs as $k=>$v) {    $PHP_VARS[$k] = $v['argum'];}extract($PHP_VARS);$JS_VARS = [    'deamon_update_time'=>10000,    'user_id'=>Access::userID(),    'domain'=>'"'.Core::$DOMAIN.'"',];if(is_array($JSVARS) && count($JSVARS) > 0) {    foreach ($JSVARS as $k=>$v) {        switch($v['type']) {            case 'int':                $JS_VARS[$k] = $v['argum'];                break;            default:                $JS_VARS[$k] = "'".$v['argum']."'";                break;        }    }}if((int)$JS_VARS['deamon_update_time'] < 3000) {    $JS_VARS['deamon_update_time'] = 10000;}include_JS('classes');if(isset($_GET['sysauth'])) {    if($row = SQL_ONE_ROW(q("SELECT * FROM `messages` WHERE `action`='".ActionsList::AUTH."' AND `params`='".db_secur($_GET['sysauth'])."' LIMIT 1"))) {        $id = (int)$row['target'];        PROFIL::AUTH_LOGIN($id);        unset($_GET['sysauth']);        q("DELETE FROM `messages` WHERE id=".$id);    }}