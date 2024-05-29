<?php
class RBS {
        static function youtubeEncode($addr) {
        $i = strripos($addr, 'watch?v=');
        if($i === false) {
            return $addr;
        } else {
            $res = substr($addr, $i+8, strlen($addr));
            return 'https://www.youtube.com/embed/'.$res;
        }
    }

    static function getImgsFromFolderID($idfolder) {
        $imgs = SUBD::getAllLinesDB('images', 'idfolder', (int)$idfolder);
        return $imgs;
    }

    static function text($id) {
        return(SUBD::getLineDB('texts', 'id', (int)$id)[Core::$LANG]);
    }

    static function textSlot($id, $numberSlot=1) {
        return(SUBD::getLineDB('texts', 'id', (int)$id)['slot'.(int)$numberSlot]);
    }

    static function textImg($id) {
        return(RBS::img(SUBD::getLineDB('texts', 'id', (int)$id)['img']));
    }

    static function getImg($shirtName) {
        $DB = new SUBD();
        $DB->enabledTable('file');
        $arr = $DB->getLine('file', 'userName', db_secur($shirtName));
        if($arr != false) {
            return $arr['name'];
        } else {
            return false;
        }
    }

    public static function resurs($nameOrPath) {
        if(file_exists('./RESURSES/'.$nameOrPath)) {
            return '/RESURSES/'.$nameOrPath;
        } else {
            $ans = SUBD::getLineDB('images', 'userName', $nameOrPath);
            if(is_array($ans)) {
                $ans = $ans['name'];
                if(file_exists('./RESURSES/'.$ans)) {
                    return '/RESURSES/'.$ans;
                } else return false;
            } else {
                return false;
            }
        }
    }

    public static function img($nameOrPath, $size=0) {
        switch($size) {
            case '0':
                if(file_exists('./IMG/SYS/'.$nameOrPath)) {
                    return './IMG/SYS/'.$nameOrPath;
                }
                break;
            case '1':

                break;
            case '2':

                break;
        }
    }

    public static function del_resurs($resurs_name) {
        if(mb_substr($resurs_name, 0, 3) === 'US_') {
            if(file_exists('./RESURSES/'.$resurs_name)) {
                $row = SUBD::getLineDB('images', 'text', $resurs_name);
                if(is_array($row)) {
                    if(unlink('./RESURSES/'.$resurs_name)) {
                        q("DELETE FROM `images` WHERE `id` = ".(int)$row['id']." ");
                        return true;
                    } else {
                        Message::addError('Ошибка физического удаления файла с сервера!..');
                        return false;
                    }
                } else {
                    Message::addError('Нет записи в таблице!..');
                    return false;
                }
            } else {
                Message::addError('Такого файла нет на сервере!..');
                return false;
            }
        } else {
            Message::addError('Не является ресурсом!..');
            return false;
        }
    }

    public static function sendPOST($url, array $params) {
        $query = http_build_query($params);
        $ch    = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    static function say($val) {
        echo '<h1 style="margin-top: 100px">'.$val.'</h1>';
        exit();
    }

    static function show($array) {
        echo '<div class="text-info-6"><pre>';
        print_r($array);
        echo '</pre></div>';
        exit();
    }

    static function SVG($name) {
        $ans = file_get_contents('./IMG/SYS/svg.svg');
        $ask = q("
                SELECT * FROM `file` WHERE 
                `user_name` = '".db_secur($name)."' OR
                `user_name` = '".db_secur($name).".svg' OR
                `sys_name` = '".db_secur($name)."' LIMIT 1
                ");
        if($ask->num_rows) {
            $row = $ask->fetch_assoc();
            if(file_exists('./DOWNLOAD/'.$row['sys_name'])) {
                return file_get_contents('./DOWNLOAD/'.$row['sys_name']);
            }
        } else {
            if(file_exists('./IMG/SYS/'.$name.'.svg')) {
                return file_get_contents('./IMG/SYS/'.$name.'.svg');
            }
        }
        return $ans;
    }

    static function get_EXP($file_name) {
        $arr = explode('.', $file_name);
        if(is_array($arr) && count($arr) > 0) {
            return mb_strtolower($arr[count($arr)-1]);
        }
        return 'file';
    }

    static function js_script_link_append_once($path_js_script) {
        if(!in_array($path_js_script, Core::$JS)) {
            Core::$LINKS_JS[] = $path_js_script;
            echo '<script src="'.$path_js_script.'?'.filemtime('.'.$path_js_script).'"></script>';
        } else {
            echo '';
        }
    }
    static function js_script_link_before_once($path_js_script) {
        $scr = '<script src="'.$path_js_script.'?'.filemtime($path_js_script).'"></script>';
        if(!in_array($scr, Core::$LINKS_START_JS)) {
            Core::$LINKS_START_JS[] = $scr;
        } else {
            echo '';
        }
    }

    static function get_extention($file_name) {
        $exp = explode('.', $file_name)[count(explode('.', $file_name))-1];
        $exp = mb_strtolower($exp);
        switch($exp) {
            case 'jpg':
            case 'jpeg':
            case 'webp':
            case 'png':
            case 'gif':
                return 'image';
            case 'mp3':
            case 'ogg':
            case 'wav':
            case 'ape':
            case 'flac':
                return 'audio';
            case 'mp4':
            case 'webm':
            case 'wmv':
            case 'avi':
                return 'video';
            case 'svg':
                return 'svg';
            case 'txt':
                return 'txt';
            case 'doc':
            case 'docx':
                return 'word';
            case 'xlsx':
                return 'tabs';
            case 'pdf':
                return 'pdf';
            default:
                return 'file';
        }
    }

    public static function GET_UPDATE() {
        $get = explode('?', $_SERVER['HTTP_REFERER'])[1];
        foreach(explode('&', $get) as $v) {
            $arrm = explode('=', $v);
            if(is_array($arrm) && count($arrm) >= 2) {
                $_GET[$arrm[0]] = $arrm[1];
            }
        }
    }

    public static function _OO_array_OO_(array $array, string $before, string $after=''): array
    {
        if($after === '') { $after = $before; }

        $addSymbols = function($element) use ($before, $after) {
            return $before . $element . $after;
        };

        return array_map($addSymbols, $array);
    }

}