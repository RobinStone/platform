<?php
class GENERAL_UPLOADER {
    public static function isseter($post) {
        if(isset($post['executer'])) {
            if(!file_exists('./CONTROLLERS/UPLOAD_GENERAL_EXECUTERS/'.$post['executer'].'.php')) {
                $ans = [
                    'status'=>'error',
                    'text'=>'Не найден затребованный обработчик!..',
                    'type'=>'load',
                ];
                echo json_encode($ans, JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    }

    public static function check($post, $files) {
        if(isset($post['executer'])) {
            $type = 'CHECKS';
            include('./CONTROLLERS/UPLOAD_GENERAL_EXECUTERS/'.$post['executer'].'.php');
        }
    }

    public static function execute($post, $sys_name, $user_name, $type_file, $last_id) {
        if(isset($post['executer'])) {
            $type = 'EXECUTE';
            $ans = [
                'status'=>'clear',
                'type'=>'load',
                'sys_name'=>$sys_name,
                'user_name'=>$user_name,
                'type_file'=>$type_file,
                'insert_last_id'=>$last_id,
                'EXECUTER_COMPLITE'=>'NOT EXECUTED',
            ];
            include('./CONTROLLERS/UPLOAD_GENERAL_EXECUTERS/'.$post['executer'].'.php');
            echo json_encode($ans, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public static function plus_counts_uploaded_files(int $count=1, $user_id='self') {
        if($user_id === 'self') { $user_id = Access::userID(); } else { $user_id = (int)$user_id; }
        $P = PROFIL::init($user_id);
        $dt = date('Y-m-d');
        $count_uploading_files = $P->get_sys_param('count_uploading_files');
        if($count_uploading_files === '') {
            $count_uploading_files = [
                'DATA'=>date('Y-m-d'),
                'COUNT'=>0,
                'ALL'=>0,
            ];
        }
        if($count_uploading_files['DATA'] !== $dt) {
            $count_uploading_files['DATA'] = $dt;
            $count_uploading_files['COUNT'] = 0;
        }
        ++$count_uploading_files['COUNT'];
        ++$count_uploading_files['ALL'];
        $P->add_sys_param('count_uploading_files', $count_uploading_files);
        return $count_uploading_files['COUNT'];
    }

    public static function access_to_upload_limit($user_id='self'): bool
    {
        if($user_id === 'self') { $user_id = Access::userID(); } else { $user_id = (int)$user_id; }
        $P = PROFIL::init($user_id);
        $dt = date('Y-m-d');
        $count_uploading_files = $P->get_sys_param('count_uploading_files');
        if($count_uploading_files === '') {
            $count_uploading_files = [
                'DATA'=>date('Y-m-d'),
                'COUNT'=>0,
                'ALL'=>0,
            ];
            $P->add_sys_param('count_uploading_files', $count_uploading_files);
        }
        if($count_uploading_files['DATA'] !== $dt) {
            $count_uploading_files['DATA'] = $dt;
            $count_uploading_files['COUNT'] = 0;
            $P->add_sys_param('count_uploading_files', $count_uploading_files);
        }
        if((int)$count_uploading_files['COUNT'] > (int)getParam('day_limit_uploads', 0)) {
            return false;
        }
        return true;
    }
}