<?php
class CHAT {
    public static function delete_all_rooms_and_chats_for_user_id(int $id_user): bool
    {
        $rows = SQL_ROWS(q("SELECT * FROM `hu_in_room` WHERE id_user=".$id_user));
        $ids = array_column($rows, 'id_room');
        $rooms = SQL_ROWS(q("SELECT room FROM room WHERE id IN (".implode(',', $ids).")"));
        foreach($rooms as $k=>$v) {
            ROOM::delete_room($v['room']);
        }
        return true;
    }
}