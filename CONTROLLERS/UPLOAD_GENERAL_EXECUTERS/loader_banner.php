<?php
//    В switch обработка 2-ух сценариев (1 - когда CHECK - проверка перед загрузкой файла в хранилише) и
//    (2 - когда EXECUTE - уже после загрузки)
//    САМОЕ ВАЖНОЕ это конечный массив $arr, всё остальное можно убирать
//    но сам SWITCH, два CASE и их массивы - СОХРАНЯЕМ, от=стальное копируем

$banner = SUBD::getLineDB('banner', 'id', $_POST['id_banner'] ?? -10);
if(!is_array($banner) || (int)$banner['owner'] !== Access::userID()) {
    $ans = [
            'status'=>'error',
            'text'=>'Запрещено менять чужой баннер!..',
            'type'=>'load',
            'EXECUTER_COMPLITE'=>'OK - ERROR SELF-ACCESS',
    ];
    echo json_encode($ans, JSON_UNESCAPED_UNICODE);
    exit;
}
switch($type) {
    case 'CHECKS':
        if(!GENERAL_UPLOADER::access_to_upload_limit()) {
            $ans = [        // ВАЖНЫЙ !!!
                'status'=>'error',
                'text'=>'Достигнут Ваш суточный предел на загрузку файлов!..',
                'type'=>'load',
                'EXECUTER_COMPLITE'=>'OK - ERROR LIMIT',
            ];
            echo json_encode($ans, JSON_UNESCAPED_UNICODE);
            exit;
        }
        break;
    case 'EXECUTE':
        $counts = GENERAL_UPLOADER::plus_counts_uploaded_files();
        $ans = [        // ВАЖНЫЙ !!!
            'status'=>'ok',
            'type'=>'load',
            'loaded_files_tooday'=>$counts,
            'sys_name'=>$sys_name,
            'user_name'=>$user_name,
            'type_file'=>$type_file,
            'insert_last_id'=>$last_id,
            'EXECUTER_COMPLITE'=>'ok',
        ];
        q("UPDATE `banner` SET `img`='".$sys_name."' WHERE `id`=".(int)$banner['id']);
        break;
}