<?php
spl_autoload_register('autoloader');

function autoloader($className) {
    include('./libs/class_'.$className.'.php');
}

$PERM = new Access();
global $PERM;
enum PROFIL_TYPE {
    case login;
    case id;
    case email;
}

enum TYPE_INTERVAL {
    case seconds;
    case minutes;
    case hours;
    case days;
}

enum mess_type {
    case text;
    case image;
    case video;
    case audio;
    case file;
    case pdf;
}

enum ALERT_TYPE:string {
    case MESSAGE = 'message';
    case ATTANTION = 'attantion';
    case WARNING = 'warning';
    case DANGER = 'danger';
    case ERROR = 'error';
}

enum MODALITY:string {
    case empty = 'empty';
    case user = 'user';
    case operator = 'operator';
    case easy_seller = 'easy-seller';
    case admin = 'admin';
    case super_admin = 'super-admin';
    case seo_operator = 'seo-operator';
}

enum PRODUCT_GROUP:string {
    case MAIN_CAT = 'shops_categorys';
    case UNDER_CAT = 'shops_undercats';
    case ACTION_LIST = 'shops_lists';
}

enum TARIF_CDEK:string {
    case EXPRESS_D_D = 'Экспресс дверь-дверь';
    case EXPRESS_D_W = 'Экспресс дверь-склад';
    case EXPRESS_W_D = 'Экспресс склад-дверь';
    case EXPRESS_W_W = 'Экспресс склад-склад';
    case EXPRESS_D_P = 'Экспресс дверь-постамат';
    case EXPRESS_W_P = 'Экспресс склад-постамат';
    case EXPRESS_P_D = 'Экспресс постамат-дверь';
    case EXPRESS_P_W = 'Экспресс постамат-склад';
    case EXPRESS_P_P = 'Экспресс постамат-постамат';
    case M_EXPRESS_D_D = 'Магистральный экспресс дверь-дверь';
    case M_EXPRESS_D_W = 'Магистральный экспресс дверь-склад';
    case M_EXPRESS_W_D = 'Магистральный экспресс склад-дверь';
    case M_EXPRESS_W_W = 'Магистральный экспресс склад-склад';
    case M_EXPRESS_D_P = 'Магистральный экспресс дверь-постамат';
    case M_EXPRESS_W_P = 'Магистральный экспресс склад-постамат';
}

Core::$DT = date('Y-m-d H:i:s');
Core::$SESSIONCODE = md5(session_id().Core::$CRYPTER_SALT_1);
SITE::set('CODE', Core::$SESSIONCODE);
SITE::$dt = Core::$DT;
SITE::$base_dir = dirname(__DIR__);


function SQL_ONE_ROW($ask):bool|array
{
    if($ask === false) {
        return false;
    }

    $arr = false;

    if(is_a($ask, 'mysqli_result')) {
        $arr = $ask->fetch_assoc();
    } else {
        $arr = $ask[0];
    }
    if(is_null($arr)) {
        $arr = false;
    }
    return $arr;
}
function SQL_ROWS($ask): array
{
    if($ask === false) {
        return [];
    }

    $arr = [];

    if(is_a($ask, 'mysqli_result')) {
        while($row = $ask->fetch_assoc()) {
            $arr[] = $row;
        }
    } else {
        $arr = $ask;
    }

    return $arr;
}
function SQL_FIELD($ask, string $field_name) {
    if ($ask === false) {
        return false;
    }
    if (is_a($ask, 'mysqli_result')) {
        $arr = $ask->fetch_assoc();
    } else {
        $arr = $ask[0] ?? null;
    }
    if (isset($arr[$field_name]) && !empty($arr[$field_name])) {
        return $arr[$field_name];
    }
    return false;
}

function SQL_ROWS_FIELD($ask, $field_name): array
{
    if($ask === false) {
        return [];
    }
    $arr = [];
    if(is_a($ask, 'mysqli_result')) {
        while($row = $ask->fetch_assoc()) {
            $arr[$row[$field_name]] = $row;
        }
    } else {
        foreach($ask as $v) {
            $arr[$v[$field_name]] = $v;
        }
    }
    return $arr;
}

/**
 * Получает на вход результат функции q (query), например
 * SELECT name FROM users WHERE id = 2 LIMIT 1
 * возвращает первое поле, которое встретит или false
 *
 * @param $ask
 * @return mixed
 */
function SQL_ONE_FIELD($ask): mixed
{
    if ($ask->num_rows) {
        $row = $ask->fetch_assoc();
        if(count($row) > 0) {
            return $row[array_key_first($row)];
        }
        return $row;
    }
    return false;
}

function wtf($array, $stop = false) {
    echo '<pre style="font-size: 13px; line-height: 16px">'.print_r($array,1).'</pre>';
    if(!$stop) {
        exit();
    }
}

/**
 * @param $query
 * @param bool $cache
 * @param int $key
 * @return mixed
 */
function q($query, bool $cache = false, int $key = 0): mixed {
    if(Core::$cache && $cache) {
        $cache_key = md5($query);
        $cache_file = './logs/Cache/' . $cache_key . '.cache';
        if (file_exists($cache_file) && (filemtime($cache_file) > (time() - Core::$cache_time))) {
            return unserialize(file_get_contents($cache_file));
        }
    }
    try {
        $res = DB::_($key)->query($query);
        if($res === false) {
            $info = debug_backtrace();    // откуда вызывалась функция полная информация
            echo '<div style="margin-top: 300px">';
            echo 'ЗАПРОС: '.$query.'<br>'.DB::_($key)->error.'<br><hr>';
            wtf($info, 1);
            echo 'ФАЙЛ - '.$info[0]['file'].'<br>СТРОКА - '.$info[0]['line'];
            echo '</div>';
            $error = 'Ошибка в запросе: '.date('Y-m-d h:i:s')."\n".$query."\n\n".'ФАЙЛ - '.$info[0]['file']."\n".'СТРОКА - '.$info[0]['line']."\n".'=======================================================';
            file_put_contents('./logs/query_errors.txt', $error."\n\n", FILE_APPEND);
            $_SESSION['errors'][] = 'ошибка в запросе';
            return false;
        } else {
            if(Core::$cache && $cache) {
                $data = $res->fetch_all(MYSQLI_ASSOC);
                file_put_contents($cache_file, serialize($data));
                $res->data_seek(0);
            }
            return $res;
        }
    } catch (Exception $e) {
        $info = debug_backtrace();    // откуда вызывалась функция полная информация
        echo '<div style="margin-top: 300px">';
        echo 'ЗАПРОС: '.$query.'<br>'.DB::_($key)->error.'<br><hr>';
        wtf($info, 1);
        echo 'ФАЙЛ - '.$info[0]['file'].'<br>СТРОКА - '.$info[0]['line'];
        echo '</div>';
        $error = 'Ошибка в запросе: '.date('Y-m-d h:i:s')."\n".$query."\n\n".'ФАЙЛ - '.$info[0]['file']."\n".'СТРОКА - '.$info[0]['line']."\n".'=======================================================';
        file_put_contents('./logs/query_errors.txt', $error."\n\n", FILE_APPEND);
        $_SESSION['errors'][] = 'ошибка в запросе';
        return false;
    }
}

function isMobile() {
    $mobile_agent_array = array('ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser');
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    // var_dump($agent);exit;
    foreach ($mobile_agent_array as $value) {
        if (str_contains($agent, $value)) return true;
    }
    return false;
}

function say($val) {
    if(!is_array($val) && !is_object($val)) {
        $info = debug_backtrace();    // откуда вызывалась функция полная информация
        $mess = date('H:i:s') . "\n" . htmlspecialchars_decode($val) . "\n" .
                'File - ' . $info[0]['file'] . '     line = ' . $info[0]['line'] . "\n" . '=============================' . "\n\n";
    } else {
        $info = debug_backtrace();
        if(is_object($val)) {
            $val->FILE_INFO = $info;
        } else {
            foreach($info as &$item_info) {
                if(isset($item_info['args'])) {
                    unset($item_info['args']);
                }
            }
            $val['FILE_INFO'] = $info;
        }
        $mess = print_r($val, true);
    }
    file_put_contents('./logs/say.txt', $mess, FILE_APPEND);
}
function say_string($val) {
    file_put_contents('./logs/say.txt', $val, FILE_APPEND);
}
function out_secur($var) {
    if(!is_array($var)) {
        $var = htmlspecialchars($var);
    } else {
        $var = array_map('out_secur', $var);
    }
    return $var;
}

function db_secur($var, $key = 0): array|string
{
    if(!is_array($var)) {
        $var = DB::_($key)->real_escape_string($var);
    } else {
        $var = array_map('db_secur', DB::_($key)->real_escape_string($var));
    }
    return $var;
}
function trimAll($var): array|string
{
    if(!is_array($var)) {
        $var = trim($var);
    } else {
        $var = array_map('trimAll', $var);
    }
    return $var;
}
function start_transaction() {
    q("start transaction;");
}
function end_transaction() {
    q("commit;");
}
function error_transaction() {
    q("rollback;");
}
function crypter($var) {
    $salt1 = Core::$CRYPTER_SALT_1;
    $salt2 = Core::$CRYPTER_SALT_2;
    return crypt(md5($salt2.$var.$salt1), $salt2);
}
function render($tpl, $arr=[]): bool|string {
    SITE::$params['TEMPLATES'][] = $tpl;
    if($tpl === '') {
        return '';
    }
    extract($arr);
    ob_start();
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/CONTROLLERS/EXE/'.$tpl.'Controller.php')) {
        include_once('./CONTROLLERS/EXE/' . $tpl . 'Controller.php');
    }
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/TEMPLATES/CSS/'.$tpl.'.css')) {
        if(!in_array($tpl, Core::$CSS)) {
            Core::$CSS[] = '<link rel="stylesheet" href="/TEMPLATES/CSS/'.$tpl.'.css?'.filemtime("./TEMPLATES/CSS/".$tpl.".css").'">';
        }
    }
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/TEMPLATES/JS/'.$tpl.'.js')) {
        if(!in_array($tpl, Core::$JS)) {
            if(isset($_GET['route']) && $_GET['route'] === 'admin' || getParam('linker') == 0) {
                Core::$JS[] = '<script src="/TEMPLATES/JS/'.$tpl.'.js?'.filemtime("./TEMPLATES/JS/".$tpl.".js").'"></script>';
            } else {
                SITE::$JS .= file_get_contents($_SERVER['DOCUMENT_ROOT'].'/TEMPLATES/JS/'.$tpl.'.js') . "\r\n";
            }
        }
    }
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/TEMPLATES/'.$tpl.'.php')) {
        include('./TEMPLATES/' . $tpl . '.php');
    } else {
        echo render('404');
    }
    return ob_get_clean();
}

function render_app_template($app, $tpl, $arr=[]) {
    if(!is_dir('./APPLICATIONS/'.mb_strtoupper($app))) {
        Message::addError('Приложеня с именем [./APPLICATIONS/'.mb_strtoupper($app).'] - не найдено...');
        return false;
    }
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/APPLICATIONS/'.mb_strtoupper($app).'/CSS/'.$tpl.'.css')) {
        if(!in_array($tpl, Core::$CSS)) {
            Core::$CSS[] = '<link rel="stylesheet" href="/APPLICATIONS/'.mb_strtoupper($app).'/CSS/'.$tpl.'.css?'.filemtime("./APPLICATIONS/".mb_strtoupper($app)."/CSS/".$tpl.".css").'">';
        }
    }
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/APPLICATIONS/'.mb_strtoupper($app).'/JS/'.$tpl.'.js')) {
        if(!in_array($tpl, Core::$JS)) {
            Core::$JS[] = '<script src="/APPLICATIONS/'.mb_strtoupper($app).'/JS/'.$tpl.'.js?'.filemtime("./APPLICATIONS/".mb_strtoupper($app)."/JS/".$tpl.".js").'"></script>';
        }
    }
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/APPLICATIONS/'.mb_strtoupper($app).'/templates/'.$tpl.'.php')) {
        include('./APPLICATIONS/'.mb_strtoupper($app).'/templates/'.$tpl.'.php');
    } else {
        echo render('404');
    }


}

function include_CSS($CSS_name) {
    $css = '<link rel="stylesheet" href="/TEMPLATES/CSS/'.$CSS_name.'.css?'.filemtime('./TEMPLATES/CSS/'.$CSS_name.'.css').'">';
    if(file_exists('./TEMPLATES/CSS/'.$CSS_name.'.css')) {
        if(!in_array($css, Core::$CSS)) {
            Core::$CSS[] = $css;
        }
    }
}
function include_CSS_once($path_CSS) {
    if($path_CSS[0] === '.') {
        $path_CSS = substr($path_CSS, 1);
    }
    if(file_exists($_SERVER['DOCUMENT_ROOT'].$path_CSS)) {
        if(!in_array($path_CSS, Core::$CSS)) {
            Core::$CSS[] = $path_CSS;
            echo '<link rel="stylesheet" href=".'.$path_CSS.'?'.filemtime($_SERVER['DOCUMENT_ROOT'].$path_CSS).'">';
        }
    }
}
function include_JS($JS_name) {
    $scr = '<script src="/TEMPLATES/JS/'.$JS_name.'.js?'.filemtime('./TEMPLATES/JS/'.$JS_name.'.js').'"></script>';
    if(file_exists('./TEMPLATES/JS/'.$JS_name.'.js')) {
        if(!in_array($scr, Core::$JS)) {
            Core::$JS[] = $scr;
        }
    }
}
function include_CDN_JS($JS_name) {
    $scr = '<script src="'.$JS_name.'"></script>';
        if(!in_array($scr, Core::$JS)) {
            Core::$JS[] = $scr;
        }
}
function include_JS_once($path_JS) {
    if(file_exists($_SERVER['DOCUMENT_ROOT'].$path_JS)) {
        if(!in_array($path_JS, Core::$JS)) {
            Core::$JS[] = $path_JS;
            echo '<script>load_JSscript_once("'.$path_JS.'?'.filemtime($_SERVER['DOCUMENT_ROOT'].$path_JS).'");</script>';
        }
    }
}

function getParam($param, $if_not_isset='') {
    $val = SUBD::getLineDB('main', 'param', $param);
    if(is_array($val)) {
        return $val['argum'];
    } else {
        if($if_not_isset !== '') {
            return $if_not_isset;
        }
        return '-';
    }
}

function setParam($param, $argum) {
    $p = getParam($param);
    if($p == '-') {
        q("
             INSERT INTO `main` SET 
             `param`             = '".db_secur($param)."',
              `argum`             = '".db_secur($argum)."',
              `paramDescription`  = '-'
            ");
    } else {
        q("
            UPDATE `main` SET 
            `argum`             = '".db_secur($argum)."'
            WHERE `param`       = '".db_secur($param)."'
            ");
    }
}

function delParam($param) {
    $p = getParam($param);
    if($p != '-') {
        q("
         DELETE FROM `main` WHERE `param` = '".db_secur($param)."'
        ");
    } else {
        echo 'Variable is not exist.';
        exit;
    }
}

function set_title($text='') {
    Core::$title = $text;
}
function set_description($text='') {
    Core::$description = $text;
}
function set_keywords($text='') {
    Core::$keywords = $text;
}
function set_h1($text='') {
    Core::$h1 = $text;
}

function send_mail($target_email, $title_mail, $body_mail) {
    $row['email'] = $target_email;

    require_once('./libs/phpmailer/PHPMailerAutoload.php');
    $mail = new PHPMailer();
    $mail->CharSet = 'utf-8';

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'hostru08.fornex.host'; //Core::$HOST;//'smtp.mail.ru';  		https://aibymental.com/																					// Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = Core::$OWNER_MAIL_LOGIN; // Ваш логин от почты с которой будут отправляться письма
    $mail->Password = Core::$OWNER_PASS_MAIL; // Ваш пароль от почты с которой будут отправляться письма
    $mail->SMTPSecure = 'ssl'; //Core::$SMTP_PROTOCOL; //'ssl';  // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465; //Core::$MAIL_PORT; //465; // TCP port to connect to / этот порт может отличаться у других провайдеров

    $mail->setFrom(Core::$OWNER_MAIL); // от кого будет уходить письмо?
    $mail->addAddress($row['email']);     // Кому будет уходить письмо


    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $title_mail;
    $mail->Body    = $body_mail;
    $mail->AltBody = '';

    if(!$mail->send()) {
        return $mail;
    } else {
        return true;
    }
}

function template($body, $header='', $footer='', $params=[]): string {
    $foot = render($footer);
    $tmp = render($body, $params);
    return render($header).$tmp.$foot;
}

function com_to_front($target_name, $params) {
    Access::set_system_message($target_name, 'system', ActionsList::COM_TO_FRONT, $params);
}

function INCLUDE_CLASS($app_name, $class_name) {
    $name = './APPLICATIONS/'.mb_strtoupper($app_name).'/libs/class_'.mb_strtoupper($class_name).'.php';
    if(file_exists($name)) {
        include_once $name;
    } else {
        Message::addError('Назначен к подключению класс приложения, которого не существует<br>'.$name);
    }
}

function t($message) {
    TELE::send_at_user_name('robin', $message);
}

if(!isset($_COOKIE['my_place'])) {                          //   Название города в формате Гродно|hrodna
    $scan_city = GEO2IP::get_info();
    $my_place = $scan_city['city']['name_ru'] . "|" . mb_strtolower($scan_city['city']['name_en']);

    if(Access::scanLevel() > 0) {
        $P = PROFIL::init(Access::userID());
        $city = $P->get('city', '-');
        if($city !== '-') {
            $my_place = $city."|".VALUES::translit($city);
        }
    }

    setcookie('my_place', $my_place, time() + 31556926, '/');
    $my_place = explode('|', $my_place);
} else {
    $my_place = explode('|', $_COOKIE['my_place']);
}

function set_type_page($type_page) {
    Core::$TYPE_PAGE = $type_page;
}
function get_type_page(): string
{
    return Core::$TYPE_PAGE;
}

function escapeArray($arr, $escapeSymbol="`"): array {
    $escapedArr = array();
    foreach ($arr as $key => $value) {
        $escapedArr[$key] = $escapeSymbol . $value . $escapeSymbol;
    }
    return $escapedArr;
}

function add_META(string $tag) {
    if(!in_array($tag, Core::$META)) {
        Core::$META[] = $tag;
    }
}

function delete_folder($folderPath) {
    if (!is_dir($folderPath)) {
        return;
    }

    $files = array_diff(scandir($folderPath), array('.', '..'));

    foreach ($files as $file) {
        $filePath = $folderPath . '/' . $file;

        if (is_dir($filePath)) {
            deleteFolder($filePath);
        } else {
            unlink($filePath);
        }
    }

    rmdir($folderPath);
}


if(Access::scanLevel() > 0) {
    $buff_admin = PROFIL::init(Access::userID())->get_sys_param('admin_panel');
    if($buff_admin == '') {
        Core::$ADMIN = [];
    } else {
        Core::$ADMIN = $buff_admin;
    }
    if(editor()) {
        include_JS('admin_panel');
    }
}

function editor(): bool
{
    if(isset(CORE::$ADMIN['editor']) && CORE::$ADMIN['editor'] == '1') {
        return true;
    }
    return false;
}

function f_($fragment_name, $file_name, $access_level_min=3) {
    if(editor() && Access::scanLevel() >= $access_level_min) {
        echo '<div class="fragment-line" data-fr-name="'.$fragment_name.'" data-page="'.$file_name.'"><button onclick="load_code(\''.$fragment_name.'\', \''.$file_name.'\')">EDIT CODE "'.$fragment_name.'"</button><div class="ace_editor" id="'.$fragment_name.'"></div></div>';
    }
}

function f_end($fragment_name) {
}

function get_user_IP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

/**
 * установить вложенное значение
 * например set_attachment_value([исходнфй массив в котором меняем], 'admin.param1.param2', 'bla-bla-bla')
 * @param array $arr
 * @param string $attachment
 * @param mixed $value
 * @return array
 */
function set_attachment_value(array $arr, string $attachment, mixed $value): array
{
    $data = explode('.', $attachment);
    $temp = &$arr; // создаем ссылку на массив для изменения
    foreach ($data as $key) {
        $temp = &$temp[$key]; // переходим к следующему уровню массива
    }
    $temp = $value; // присваиваем значение
    unset($temp); // удаляем ссылку
    return $arr;
}

/**
 * удалить вложенное значение
 * например delete_attachment_key([исходнфй массив в котором удаляем], 'admin.param1.param2')
 * @param array $arr
 * @param string $attachment
 * @return array
 */
function delete_attachment_key(array $arr, string $attachment): array
{
    $data = explode('.', $attachment);
    $temp = &$arr;
    $lastKey = array_pop($data);
    foreach ($data as $key) {
        if (isset($temp[$key])) {
            $temp = &$temp[$key];
        } else {
            return $arr;
        }
    }
    if (isset($temp[$lastKey])) {
        unset($temp[$lastKey]);
    }
    return $arr;
}

/**
 * получить вложенное значение
 * например get_attachment_value([исходнфй массив из которого получаем], 'admin.param1.param2')
 * @param array $array
 * @param string $attachment
 * @param string $if_not_exist
 * @return string|array
 */
function get_attachment_value(array $array, string $attachment, string $if_not_exist=''): string|array
{
    $arr = explode('.', $attachment);
    if(count($arr) > 0 && isset($array[$arr[0]])) {
        $buff = $array[$arr[0]];
        unset($arr[0]);
        foreach($arr as $v) {
            if(isset($buff[$v])) {
                $buff = $buff[$v];
            } else {
                return '';
            }
        }
        return $buff;
    }
    if($if_not_exist !== '') {
        return $if_not_exist;
    }
    return '';
}

/**
 * СОБИРАЕТ ВСЕ CSS ФАЙЛЫ В ОДИН
 * контролируется админкой:
 * ПАРАМЕТР (linker), если = 1 - работает вывод из буффера, если 0 - рендерится всякий раз заново
 * !!! РЕКОМЕНДУЕТСЯ !!! чистить кэш в папке SITE или в админке кнопка метёлки
 * @param string $html
 * @return void
 */
function linker(string &$html) {
    if(SEO::$template_name !== '') {
        $tmp_name = './SITE/'.SEO::$template_name.'.css';
        $tmp_name_JS = './SITE/'.SEO::$template_name.'_s.js';
        if(!file_exists($tmp_name)) {
            preg_match_all('~<link rel="stylesheet" href="(.*?)"~', $html, $matches);
            $cssLinks = $matches[1];
            $css = "";
            foreach ($cssLinks as $v) {
                $name = explode('?', $v)[0];
                if(!str_contains($name, 'http')) {
                    $name = "." .$name;
                }
                $css .= file_get_contents($name) . "\r\n";
            }
            file_put_contents($tmp_name, $css);
            file_put_contents($tmp_name_JS, SITE::$JS);
        }
        $html = preg_replace('~<link rel="stylesheet" href="(.*?)".*?>~', '', $html);
        $html = preg_replace('<!--linker-CSS-->', 'link rel="stylesheet" href="'.Core::$DOMAIN.'SITE/'.SEO::$template_name.'.css"', $html);
        $html = preg_replace('<!--linker-JS-->', 'script src="'.Core::$DOMAIN.'SITE/'.SEO::$template_name.'_s.js"></script', $html);
    }
}

function get_product_schema() {
    if (file_exists('./CONTROLLERS/SCHEMAS/product_fields.php')) {
        $rows = require './CONTROLLERS/SCHEMAS/product_fields.php';
        return $rows;
    } else {
        wtf('not schema - ./CONTROLLERS/SCHEMAS/product_fields.php');
    }
}

function get_schema(string $name) {
    if(file_exists('./CONTROLLERS/SCHEMAS/'.$name.'.php')) {
        return require './CONTROLLERS/SCHEMAS/'.$name.'.php';
    } else {
        wtf('not schema - ./CONTROLLERS/SCHEMAS/'.$name.'.php');
    }
}

function s_crypt($string): string
{
    return password_hash($string, PASSWORD_DEFAULT);
}

function s_crypt_scan($string, $hash): bool
{
    return password_verify($string, $hash);
}

SITE::$my_place  = $my_place;
SITE::$profil = PROFIL::init(Access::userID());
SITE::$GEO = GEONAMER::get_current_position();

if(isset($_SESSION['user']['level'])) {
    if($_SESSION['user']['level'] > 0) {
        SITE::$USER_DIR = Core::$DOMAIN . "APPLICATIONS/SHOPS/user_storages/" . $_SESSION['user']['id'] . "/";
        SITE::$USER_LOCAL_DIR = "./APPLICATIONS/SHOPS/user_storages/" . $_SESSION['user']['id'] . "/";
    }
}

if(isset($_COOKIE['personal'])) {
    SITE::$personal = $_COOKIE['personal'];
} else {
    SITE::$personal = md5(date('Y-m-d H:i:s').'kwnef'.rand(10000, 99999));
    setcookie('personal', SITE::$personal, time() + 31556926, '/');
}

if(isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') {
    SITE::$theme = 'dark';
}

Core::$YANDEXGEOCODER = getParam('YANDEXGEOCODER');
Core::$SUGGEST_GEOCODER = getParam('SUGGEST_GEOCODER');

include_once ('./CONTROLLERS/preloader.php');
