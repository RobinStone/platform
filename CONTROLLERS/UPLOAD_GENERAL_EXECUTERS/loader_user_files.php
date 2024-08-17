<?php
//Этот обработчик запускается из general_fields.js путём добавления инструкции
//params_general['include'] = true;

$user_id = SITE::$user_id;
$path = isset($_POST['path']) ? explode("/", $_POST['path']) : false;
if(!$path || $user_id === -1 || count($path) === 0) {
    $ans = [        // ВАЖНЫЙ !!!
        'status'=>'error',
        'text'=>'Ошибка разрешений',
        'type'=>'load',
        'EXECUTER_COMPLITE'=>'OK - ERROR LOW USER LEVEL',
    ];
    echo json_encode($ans, JSON_UNESCAPED_UNICODE);
    exit;
}
if($path[0] !== 'home') {
    $ans = [
        'status'=>'error',
        'text'=>'Ошибка пути',
        'type'=>'load',
        'EXECUTER_COMPLITE'=>'OK - ERROR PATH NOT CORRECT',
    ];
    echo json_encode($ans, JSON_UNESCAPED_UNICODE);
    exit;
} else {
    unset($path[0]);
}
$path = './APPLICATIONS/SHOPS/user_storages/'.Access::userID()."/".implode("/", $path);

if(!is_dir($path)) {
    $ans = [
        'status'=>'error',
        'text'=>'Ошибка пути total-home',
        'type'=>'load',
        'EXECUTER_COMPLITE'=>'OK - ERROR PATH NOT CORRECT (not exists home-dir)',
    ];
    echo json_encode($ans, JSON_UNESCAPED_UNICODE);
    exit;
}

if(mb_substr($path, -1) !== "/") {
    $path .= "/";
}

$ans = [
    'status'=>'error',
    'text'=>'Неизвестная ошибка загрузки файла',
    'type'=>'load',
    'EXECUTER_COMPLITE'=>'OK - ERROR UNKNOWN...',
];

if(isset($type) && $type === 'EXECUTE') {
    if(isset($_FILES) && !empty($_FILES)) {
        if (isset($_FILES['userfile'])) {
            $expObj = new SplFileInfo($_FILES['userfile']['name']);
            if(file_exists($path.$_FILES['userfile']['name'])) {
                $user_name = pathinfo($expObj->getBasename(), PATHINFO_FILENAME)."_copy".time();
            } else {
                $user_name = pathinfo($expObj->getBasename(), PATHINFO_FILENAME);
            }

            $exp = mb_strtolower($expObj->getExtension());
            $type_file = RBS::get_extention($user_name.".".$exp);

            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $path.$user_name.".".$exp)) {
                $ans = [
                    'status'=>'ok',
                    'type'=>'load',
                    'sys_name'=>$user_name.".".$exp,
                    'user_name'=>$user_name.".".$exp,
                    'type_file'=>$type_file,
                ];
            }

        }
    }
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
exit;