<?php
class MAILER_BUILDER {
    public static function renderer($name='new') {
        $mail = SUBD::getLineDB('mailer', 'name', db_secur($name));
        if(is_array($mail)) {
            return $mail['html'];
        }
        return 'Нет шаблона с таким именем...';
    }

    static public function transform_template($template, $user_id=-1, $prog_array=[]) {
        $result = [];
        while (preg_match('|!!(.*?)!!|u', $template, $result)) {
            $com = explode('.', strip_tags($result[1]));
            $ans = '';
            if($com[1] === 'id' && $user_id !== -1 && count($prog_array) === 0) {
                $com[1] = (int)$user_id;
            } elseif(($com[1] === 'id' || $com[1] === 'self') && $user_id == -1) {
                $com[1] = Access::userID();
            } elseif($user_id === 'self' || $com[1] === 'self') {
                $com[1] = Access::userID();
            } elseif($com[1] === 'person') {
                $com[1] = (int)$user_id;
            }
            if(count($com) === 3) {
                $ans = SUBD::getLineDB($com[0], 'id', (int)$com[1]);
            } elseif(count($com) > 3 && $com[2] === 'params') {
                if((int)$com[1] === -1) {
                    return '<h1>В параметр !!users.person... - не передан ID</h1>';
                }
                $row = SUBD::getLineDB($com[0], 'id', (int)$com[1]);
                $ans = VALUES::getParamFromString($row['params'], $com[3]);
                if(isset($com[3]) && ($com[3] === 'img' || $com[3] === 'avatar')) {
                    if(file_exists('./DOWNLOAD/'.$ans)) {
                        $ans = Core::$DOMAIN.'DOWNLOAD/'.$ans;
                    } elseif(file_exists('./img/'.$ans)) {
                        $ans = Core::$DOMAIN.'img/'.$ans;
                    }
                }
            } elseif(count($com) > 3 && $com[2] === 'field') {
                $ans = SUBD::getLineDB($com[0], 'id', (int)$com[1])[$com[3]];
            }
            if(isset($com[2]) && ($com[2] === 'img' || $com[2] === 'avatar')) {
                if(file_exists('./DOWNLOAD/'.$ans)) {
                    $ans = Core::$DOMAIN.'DOWNLOAD/'.$ans;
                } elseif(file_exists('./img/'.$ans)) {
                    $ans = Core::$DOMAIN.'img/'.$ans;
                }
            }
            if($com[0] === 'code' && count($prog_array) > 0 && count($com) >= 2) {
                if(isset($prog_array[$com[1]])) {
                    $ans = $prog_array[$com[1]];
                }
            }

            $template = preg_replace('|!!(.*?)!!|u', $ans, $template, 1);

        }
        return $template;
    }

    public static function get_all_codes_from_template($tmp): array
    {
        $ans = [];
        preg_match_all('/!!(.*?)!!/', $tmp, $matches);
        if(count($matches) < 2) {
            return $ans;
        }
        foreach($matches[1] as $v) {
            $arr = explode('.', $v);
            if(count($arr) >= 2 && $arr[0] === 'code') {
                $ans[] = $arr[1];
            }
        }
        return $ans;
    }

    public static function send_template_mail($template_name, $title, $addr_list_with_codes=[]): array
    {
        $tmp = SUBD::getLineDB('mailer', 'name', db_secur($template_name))['html'];
        $codes = self::get_all_codes_from_template($tmp);
        $sended = [];
        foreach($addr_list_with_codes as $k=>$v) {
            foreach($codes as $kk) {
                if(!isset($v[$kk])) { $v[$kk] = ''; }
            }
            $sended[] = MAIL::sender($k, $title, self::transform_template($tmp, -1, $v));
        }
        return $sended;
    }
}