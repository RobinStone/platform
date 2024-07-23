<?phpclass BASKET {    private string $code = '';    private array $basket = [];    private array $products_ids = [];    private string $type_obj = '';    private bool $auth_user = false;    private int $user_id = -1;    public function __construct($cokies_code, $type_basket='basket') {        if(Access::scanLevel() > 0) {            $this->auth_user = true;            $this->user_id = Access::userID();            $type_basket .= '-id-user';        }        $this->type_obj = $type_basket;        if($cokies_code === '') {            $cokies_code = md5('ro364fghf3423'.date('d.m.Y H:i:s').rand(10000,99999).$type_basket);            $this->code = $cokies_code;            setcookie($type_basket, $cokies_code, time() + 31556926, '/');            $_SESSION['basket'] = $cokies_code;            if($this->auth_user) {                q("INSERT INTO `basket` SET `cokies`='".$cokies_code."', `id_user`=".$this->user_id.", `prod_ids`='', `dt`='".date('Y-m-d H:i:s')."', `ser`=''");            } else {                q("INSERT INTO `basket` SET `cokies`='".$cokies_code."', `prod_ids`='', `dt`='".date('Y-m-d H:i:s')."', `ser`=''");            }//            t('new COOKIES ['.$type_basket.'] - '.$cokies_code);        } else {            $row = SUBD::getLineDB('basket', 'cokies', $cokies_code);            if(is_array($row) && count($row)>0) {//                t('FIND-'.$cokies_code);                $this->code = $cokies_code;                if($row['ser'] !== '' && $row['ser'] !== '-') {                    if($ans = unserialize($row['ser'])) {                        $this->basket = $ans;                    } else {                        $this->basket = [];                    }                } else {                    $this->basket = [];                }                $ids = explode('|', $row['prod_ids']);                foreach($ids as $v) {                    if($v !== '') {                        $this->products_ids[] = $v;                    }                }                $this->clear_deleted_shops_from_basket();                $_SESSION['basket'] = $cokies_code;            } else {                $this->code = $cokies_code;                if($this->auth_user) {                    q("INSERT INTO `basket` SET `cokies`='".$cokies_code."', `id_user`=".$this->user_id.", `prod_ids`='', `dt`='".date('Y-m-d H:i:s')."', `ser`=''");//                    t('new COOKIES (apply old coocies) ['.$type_basket.'] - '.$cokies_code);                } else {                    q("INSERT INTO `basket` SET `cokies`='".$cokies_code."', `prod_ids`='', `dt`='".date('Y-m-d H:i:s')."', `ser`=''");//                    t('new COOKIES (apply old coocies) ['.$type_basket.'] - '.$cokies_code);                }                $_SESSION['basket'] = $cokies_code;            }        }    }    public function get_count(): int {        return count($this->products_ids);    }    public function clear_deleted_shops_from_basket() {        $changed = false;        foreach($this->products_ids as $v) {            $shop_id = (int)explode('_', $v)[0];            if(!SQL_ONE_ROW(q("SELECT id FROM shops WHERE id=".$shop_id." LIMIT 1"))) {                $this->change_count($v, 0, false);                $changed = true;            }        }        if($changed) {            $this->save();        }    }    public function isset_in_basket($code_product): bool {        if(in_array($code_product, $this->products_ids)) {            return true;        }        return false;    }    public function get_basket(): array {        $ans = [];        $ans_buff = [];        if(count($this->products_ids) > 0) {            INCLUDE_CLASS('shops', 'shop');            foreach($this->products_ids as $v) {                $arm = explode('_', $v);                if(count($arm) >= 2) {                    $ans_buff[] = [                        'shop_id' => $arm[0],                        'product_id' => $arm[1],                    ];                }            }            $arr = SHOP::get_products_list($ans_buff, true);//            wtf($arr);            foreach($arr as $v) {                $key = $v['ITEM']['code'];                $v['COUNT'] = (int)$this->basket[$key]['COUNT'];                $ans[$key] = $v;            }        }        return $ans;    }    public function change_count($code_product, $count, $autosave=true): bool    {        $count = (int)$count;        $id_product = $code_product;        if($count <= 0) {            if(in_array($id_product, $this->products_ids)) {                $key = array_search($id_product, $this->products_ids);                unset($this->products_ids[$key]);                unset($this->basket[$id_product]);            }        } else {            if(!in_array($id_product, $this->products_ids)) {                $this->add_in_basket($id_product, $count, false);            } else {                $this->basket[$id_product]['COUNT'] = (int)$count;            }        }        if($autosave) {            $this->save();        }        return true;    }    public function add_in_basket(string $code_product, int $count, bool $autosave=true): bool    {        if(!in_array($code_product, $this->products_ids)) {            $id_product = $code_product;            $this->products_ids[] = $id_product;            $this->basket[$id_product]['COUNT'] = $count;            if($autosave) {                $this->save();            }        } else {            Message::addError('Товар с таким ID уже добавлен в корзину');            return false;        }        return true;    }    public function save() {        $pr = implode('|', $this->products_ids);        setcookie($this->type_obj, $this->code, time() + 31556926, '/');        q("        UPDATE `basket` SET         `prod_ids`='".$pr."',         `dt`='".date('Y-m-d H:i:s')."',         `ser`='".serialize($this->basket)."'         WHERE `cokies`='".$this->code."'");    }    public function remove_basket_complite(): bool {        setcookie($this->type_obj, $this->code, time() -1, '/');        q("DELETE FROM `basket` WHERE `cokies`='".$this->code."'");        return true;    }    public function verify_in_basket_product($products_array) {        if(Access::scanLevel() > 0) {            $cokies = $_COOKIE['basket-id-user'] ?? '';        } else {            $cokies = $_COOKIE['basket'] ?? '';        }        $codes = array_keys($products_array);        foreach($codes as $k=>$v) {            $codes[$k] = "'".$v."'";        }        $arr = [];        if(count($codes) > 0) {            $arr = $this->products_ids;        }        foreach($products_array as $k=>$v) {            if(in_array($k, $arr)) {                $v['IN_BASKET'] = 1;            } else {                $v['IN_BASKET'] = 0;            }            $products_array[$k] = $v;        }        return $products_array;    }    public static function get_cookies_code_from_user_id(int $user_id) {        return SUBD::get_field_from_table('basket', 'cokies', 'id_user', $user_id, '');    }}