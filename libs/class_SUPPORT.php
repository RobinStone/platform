<?phpclass SUPPORT {    public static function get_active_service(): array    {        $rows = SQL_ROWS_FIELD(q("SELECT id, id_target FROM `call_rooms` WHERE `prefix`='support' AND `active`=1 "), 'id');        if(count($rows) === 0) {            return [];        }        $ids = array_column($rows, 'id_target');        $PP = [];        foreach($ids as $v) {            $P = new PROFIL($v);            $PP[] = [                'NAME'=>$P->get('name'),                'IMG'=>$P->get('avatar'),                'ID'=>(int)$v,            ];        }        return $PP;    }    public static function user_is_support(int $id_user): bool|array    {        return SQL_ONE_ROW(q("SELECT * FROM `call_rooms` WHERE `id_target`=".$id_user." AND `active`=1 LIMIT 1"));    }}