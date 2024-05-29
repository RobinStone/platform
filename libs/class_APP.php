<?phpclass APP {    public $folders = [];    function __construct() {        Core::$CSS[] = '<link rel="stylesheet" href="./CSS/apps.css?'.filemtime("./CSS/apps.css").'">';        Core::$JS[] = '<script src="./JS/apps.js?'.filemtime("./JS/apps.js").'"></script>';        $this->folders = $this->scan_folders();//        say($this->folders);    }    function render_menu_apps() {        ob_start();        $level = Access::scanLevel();        $modality = Access::scanModality();        foreach($this->folders as $k=>$v) {            $dis = '';            if($v['enabled'] !== 1) {                $dis = ' disabled ';            }            if($level >= 6 || ($v['name'] === 'SEO' && $modality === 'seo-operator')) {                echo '<button data-name-btn="' . $v['name'] . '" title="' . $v['descr'] . '" class="app-start-btn btn-gray btn-gray-text not-border micro-btn btn-img-with-text ' . $dis . '"><img width="20" height="20" src="' . $v['icon'] . '"><span>' . $v['name'] . '</span></button>';                if (file_exists('./APPLICATIONS/' . $v['name'] . '/autostart.js')) {                    Core::$JS[] = '<script src="./APPLICATIONS/' . $v['name'] . '/autostart.js?' . filemtime("./APPLICATIONS/" . $v['name'] . "/autostart.js") . '"></script>';                }            }        }        echo ob_get_clean();    }    private function scan_folders(): array {        $ans = [];        $folders = array_diff(scandir('./APPLICATIONS'), array('.', '..'));        $i = 0;        $apps_status = SQL_ROWS_FIELD(q("SELECT * FROM `main` WHERE `type`='app' "), 'param');        foreach ($folders as $folder) {            if(is_dir('./APPLICATIONS' .'/'.$folder)) {                $ans[$i]['name'] = $folder;                $ans[$i]['enabled'] = 0;                $ans[$i]['DIR'] = './APPLICATIONS' .'/'.$folder.'/';                $ans[$i]['descr'] = 'Нет описания.';                if(file_exists('./APPLICATIONS/'.$folder.'/about.txt')) {                    $ans[$i]['descr'] = file_get_contents('./APPLICATIONS/'.$folder.'/about.txt');                }                $files = glob('./APPLICATIONS/'.$folder.'/*.*');                foreach ($files as $file) {                    $basename = pathinfo($file, PATHINFO_FILENAME);  // имя без расширения                    if ($basename == 'icon') {                        $ans[$i]['icon'] = $file;                        break;                    }                }                if(isset($apps_status['app_'.$folder])) {                    if($apps_status['app_'.$folder]['argum'] == '1') {                        $ans[$i]['enabled'] = 1;                    }                } else {                    q("                    INSERT INTO `main` SET                     `param` = 'app_".db_secur($folder)."',                     `argum` = '0',                    `type` = 'app',                    `paramDescription` = 'Приложение',                    `js` = 0                    ");                    Message::addError('В таблице "main" не было зарегистрировано приложение "'.$folder.'".<br>Система зарегистрировала его самостоятельно. Однако для его работы, необходимо запустить его.');                }            }            ++$i;        }        return $ans;    }}