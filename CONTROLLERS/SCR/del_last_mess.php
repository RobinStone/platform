<?php
$messer = $red->chanal_last_mess($ROOM, 1, 1);
if(count($messer) > 0) {
    $messer = explode('~~', $messer[0]);
    if($ID == $messer[0]) {
        $count_item = $red->chanal_length($ROOM)-1;
        $red->chanal_del_item($ROOM, $count_item);
        echo 'ans|del_last_mess_ok';
    }
}