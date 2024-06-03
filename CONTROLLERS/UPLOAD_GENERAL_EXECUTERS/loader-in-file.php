<?php
//    В switch обработка 2-ух сценариев (1 - когда CHECK - проверка перед загрузкой файла в хранилише) и
//    (2 - когда EXECUTE - уже после загрузки)
//    САМОЕ ВАЖНОЕ это конечный массив $arr, всё остальное можно убирать
//    но сам SWITCH, два CASE и их массивы - СОХРАНЯЕМ, от=стальное копируем

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
        $user_params = [];
        if($type_file === 'audio') {
            AUDIO::create_preview($sys_name);
            $user_params = $post;
//            q("UPDATE file SET params='".$_POST['long']."' WHERE id=".(int)$last_id);
        }
        if(isset($post['change_cataloger_image'])) {
            $type = $post['change_cataloger_image'];
            $id = (int)$_POST['cataloger_id'];
            switch($type) {
                case 'maincat':
                    q("UPDATE `shops_categorys` SET `logo_img` = '".$sys_name."' WHERE `id`=".$id);
                    break;
                case 'undercat':
                    q("UPDATE `shops_undercats` SET `logo_img` = '".$sys_name."' WHERE `id`=".$id);
                    break;
                case 'actionlist':
                    q("UPDATE `shops_lists` SET `logo_img` = '".$sys_name."' WHERE `id`=".$id);
                    break;
            }
        }
        $ans = [        // ВАЖНЫЙ !!!
            'status'=>'ok',
            'type'=>'load',
            'loaded_files_tooday'=>$counts,
            'sys_name'=>$sys_name,
            'user_name'=>$user_name,
            'type_file'=>$type_file,
            'insert_last_id'=>$last_id,
            'EXECUTER_COMPLITE'=>'ok',
            'user_params'=>$user_params,
        ];
        break;
}