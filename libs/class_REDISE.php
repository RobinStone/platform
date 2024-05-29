<?php
class REDISE {
    private $red;

    function __construct() {
        include_once __DIR__.'/../vendor/autoload.php';
        $redis = new Predis\Client();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth(Core::$REDISAUTH);
        $this->red = $redis;
    }

    public function obj_set($obj_name, $param, $argum): int {
        return $this->red->hset($obj_name, $param, $argum);
    }
    public function obj_get($obj_name, $param): string {
        return (string)$this->red->hget($obj_name, $param);
    }
    public function obj_del_item($obj_name, $param_arr): int {
        return (int)$this->red->hdel($obj_name, $param_arr);
    }
    public function obj_del_item_argum($obj_name, $argum): bool {
        $arr = $this->red->hgetall($obj_name);
        foreach($arr as $k=>$v) {
//            say($v.' === '.$argum);
            if($v == $argum) {
                $this->red->hdel($obj_name, $k);
            }
        }
        return true;
    }
    public function isset($key): bool {
        return (bool)$this->red->exists($key);
    }
    public function obj_isset($obj_name, $param_arr): bool {
        return (bool)$this->red->hexists($obj_name, $param_arr);
    }
    public function obj_del_items_all($obj_name): int {
        return $this->red->del($obj_name);
    }
    public function obj_all($obj_name): array {
        return $this->red->hgetall($obj_name);
    }
    public function get($key): ?string
    {
        return $this->red->get($key);
    }
    public function set($key, $val): ?string
    {
        return $this->red->set($key, $val);
    }
    public function del($key): bool
    {
        return (bool)$this->red->del($key);
    }
    public function inc($key): int
    {
        return (int)$this->red->incr($key);
    }
    public function dec($key): int
    {
        return (int)$this->red->decr($key);
    }
    public function add_in_group($name_group, $value): bool {
        return (bool)$this->red->sadd($name_group, [$value]);
    }
    public function rem_from_group($name_group, $value): bool {
        return (bool)$this->red->srem($name_group, $value);
    }
    public function isset_in_group($name_group, $value): bool {
        return (bool)$this->red->sismember($name_group, $value);
    }
    public function count_in_group($name_group): int {
        return $this->red->scard($name_group);
    }
    public function group_all($name_group): array {
        return $this->red->smembers($name_group);
    }
    public function get_all_keys():array
    {
        return $this->red->keys('*');
    }
    public function clear_all(): bool {
        $this->red->flushall();
        return true;
    }
    public function mess_send(string $chanal, int $user_id, string $mess, mess_type $type=mess_type::text): bool {
        $t = match ($type) {
            mess_type::file => 'f',
            mess_type::image => 'i',
            mess_type::pdf => 'p',
            mess_type::video => 'v',
            mess_type::audio => 'a',
            default => 't',
        };
        $this->red->rpush($chanal, [$user_id."~~".date('Y-m-d H:i:s').'~~'.$t.'~~'.$mess]);
        return true;
    }
    public function send_just_text($chanal, $text): bool
    {
        $this->red->rpush($chanal, [$text]);
        return true;
    }
    public function get_mess_arr($chanal, $offset, $count): array {
        if($count === -1) {
            return $this->red->lrange($chanal, (int)$offset, -1);
        } else {
            return $this->red->lrange($chanal, (int)$offset, (int)($offset+$count-1));
        }
    }
    public function chanal_delete($chanal_name): bool {
        return $this->red->del($chanal_name);
    }
    public function chanal_length($chanal_name): int {
        return (int)$this->red->llen($chanal_name);
    }
    public function chanals_all(): array
    {
        $ans = [];
        foreach($this->get_all_keys() as $key) {
            if ($this->red->type($key) == 'list') {
                $ans[] = $key;
            }
        }
        return $ans;
    }
    public function chanal_last_mess($chanal_name, $count, $pagin_num): array
    {
        $all = $this->chanal_length($chanal_name);
        if($pagin_num <= 0) { $pagin_num = 1; }
        $from = $all - ($pagin_num * $count);
        if($from < 0) { $from = 0; }
        return $this->get_mess_arr($chanal_name, $from, $count);
    }
    public function find_index($chanal_name, $full_string): int
    {
        $all = $this->get_mess_arr($chanal_name, 0, -1);
        foreach($all as $k=>$v) {
            if($v === $full_string) {
                return (int)$k;
            }
        }
        return -1;
    }
    public function get_message_of_chanal_id($chanal_name, $id_item) {
        $all = $this->get_mess_arr($chanal_name, 0, -1);
        if(isset($all[$id_item])) {
            return $all[$id_item];
        }
        return false;
    }
    public function replace_item($chanal_name, $id_old_item, $new_item): bool {
        $old_item = $this->get_message_of_chanal_id($chanal_name, $id_old_item);
        if($old_item !== false) {
            $new_item = Access::userID().'~~'.date('Y-m-d H:i:s').'~~c~~'.$new_item;
            $this->red->linsert($chanal_name, 'AFTER', $old_item, $new_item);
            $this->red->lrem($chanal_name, 1, $old_item);
            return true;
        } else {
            return false;
        }
    }
    public function chanal_del_item($chanal_name, $id_item): bool {
        $old_item = $this->get_message_of_chanal_id($chanal_name, $id_item);
        if($old_item !== false) {
            $this->red->lrem($chanal_name, 1, $old_item);
            return true;
        } else {
            return false;
        }
    }
    public function chanal_archive($chanal_name): bool {
        $arr = $this->red->lrange($chanal_name, 0, -1);
        if(is_array($arr) && count($arr) > 0) {
            if(!is_dir('./RESURSES/ARCHIVE')) {
                return 'Не существует директории ./RESURSES/ARCHIVE';
            }
            if(file_put_contents('./RESURSES/ARCHIVE/'.$chanal_name.'.archive', serialize($arr))) {
                $this->chanal_delete($chanal_name);
                return true;
            } else {
                return 'Не удалось создать архив по неизвестной причине...';
            }
        } else {
            return 'Этот канал не содержит ни одной фразы. Нечего архивировать...';
        }
    }
    public function chanal_recovery($chanal_name): array {
        if(file_exists('./RESURSES/ARCHIVE/'.$chanal_name.'.archive')) {
            $arr = unserialize(file_get_contents('./RESURSES/ARCHIVE/'.$chanal_name.'.archive'));
            if(count($arr) > 0) {
                foreach($arr as $v) {
                    $this->red->rpush($chanal_name, [$v]);
                }
            }
            unlink('./RESURSES/ARCHIVE/'.$chanal_name.'.archive');
            return $arr;
        } else {
            return [];
        }
    }
    public function get_all_numbers_of_support_service(): array {
        return $this->obj_all('CALLS_SUPPORT');
    }
    public function set_time_live(string $name_key, TYPE_INTERVAL $type_interval, int $count) {
        switch($type_interval) {
            case TYPE_INTERVAL::minutes:
                $count *=60;
                break;
            case TYPE_INTERVAL::hours:
                $count *=3600;
                break;
            case TYPE_INTERVAL::days:
                $count *=86400;
                break;
            default:

                break;
            }
        $this->red->expire($name_key, $count);
    }
//    public function add_attantion_message_for($from_room, $text, $target_room) {
//        $this->obj_set()
//    }
}