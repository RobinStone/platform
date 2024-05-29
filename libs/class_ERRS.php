<?php
class ERRS {
    public static function render() {
        $errs = $_SESSION['errs'] ?? [];
        echo '<script>';
        foreach($errs as $k=>$v) {
            echo 'say(\''.$v['message'].'\', '.(int)$v['type'].');';
            unset($_SESSION['errs'][$k]);
        }
        echo '</script>';
    }
    public static function add_error($txt, $type=1) {
        $_SESSION['errs'][] = [
            'type'=>(int)$type,
            'message'=>db_secur($txt)
        ];
    }
}