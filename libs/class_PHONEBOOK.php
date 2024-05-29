<?php
class PHONEBOOK {
    public static function get_contacts($id_user): array
    {
        return SQL_ROWS("SELECT * FROM phone_book WHERE  ORDER BY datatime DESC");
    }
    public static function add_contact(int $my_id_for_phone_book, string $room, int $id_hu_in_room): bool
    {
        if(!SQL_ONE_ROW(q("
                SELECT id FROM phone_book WHERE 
                id_user=".$my_id_for_phone_book." AND 
                room='".db_secur($room)."' LIMIT 1"))
        ) {
            if($user = SQL_ONE_ROW(q("
             SELECT users.avatar, users.login, hu_in_room.id_user FROM users 
             LEFT JOIN hu_in_room ON 
             users.id = hu_in_room.id_user
             WHERE 
             hu_in_room.id=".$id_hu_in_room."
            "))) {
                q("
                INSERT INTO phone_book SET
                id_user=" . $my_id_for_phone_book . ",
                logo='" . db_secur($user['avatar']) . "',
                title='" . db_secur($user['login']) . "',
                room='" . db_secur($room) . "',
                datatime='" . date('Y-m-d H:i:s') . "',
                id_hu_in_room=".$id_hu_in_room.",
                descr=''
                ");
                return true;
            }
        }
        return false;
    }
    public static function delete_contact(int $id_user, string $room) {
        q("DELETE FROM phone_book WHERE id_user=".$id_user." AND room='".db_secur($room)."' LIMIT 1");
    }
    public static function change_image_for_phone_book(int $contact_id, string $img) {
        q("UPDATE phone_book SET logo='".db_secur($img)."' WHERE id=".$contact_id);
    }
    public static function change_title_for_phone_book(int $contact_id, string $title) {
        q("UPDATE phone_book SET title='".db_secur($title)."' WHERE id=".$contact_id);
    }
    public static function update_phone_book_datatime(int $id) {
        q("UPDATE phone_book SET datatime='".date('Y-m-d H:i:s')."' WHERE id=".$id);
    }
}
