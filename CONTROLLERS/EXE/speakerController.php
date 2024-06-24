<?php
//wtf($_GET, 1);

if(Access::scanLevel() < 1 || empty($_GET['name'])) {
    echo 'Access denied';
    exit;
}

include_CSS('chat');
include_CSS('profil');
include_JS('chat');

$users_in_room = SQL_ROWS_FIELD(q("
    SELECT `id`, `login` FROM `users` WHERE 
    `login` LIKE '".db_secur($_GET['name'])."\_%' AND
    `id` <> ".Access::userID()."
    LIMIT 30"), 'id');


//wtf($users_in_room, 1);
