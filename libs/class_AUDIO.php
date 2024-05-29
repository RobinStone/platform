<?php
class AUDIO {
    public static function create_preview($sys_name) {
        $audioFilePath = './DOWNLOAD/'.$sys_name;
        if(file_exists($audioFilePath)) {
            $outputImagePath = './IMG/AUDIO/'.explode('.', $sys_name)[0].'.png';
            $output = shell_exec("ffmpeg -y -i $audioFilePath -filter_complex \"showwavespic=s=150x54:colors=008000\" -frames:v 1 $outputImagePath 2>&1");
        }
    }
    public static function delete(array|string $file_name) {
        $ans = false;
        $all = [];
        $ids = [];
        if(!is_array($file_name)) {
            $all[] = $file_name;
        } else {
            $all = $file_name;
        }
        if(count($all) > 0) {
            $all = RBS::_OO_array_OO_($all, "'");
            $rows = SQL_ROWS_FIELD(q("
            SELECT * FROM file WHERE
            sys_name IN (" . implode(',', $all) . ") AND 
            type='audio' AND
            SYS=1
            "), 'id');
            if(count($rows) > 0) {
                $ids = array_column($rows, 'id');
                q("DELETE FROM file WHERE id IN (".implode(',',$ids).") ");
                $dir = dirname(__DIR__);
                foreach($rows as $v) {
                    $name = explode('.',$v['sys_name'])[0].".png";
                    if(file_exists($dir.'/DOWNLOAD/'.$v['sys_name'])) {
                        unlink($dir.'/DOWNLOAD/'.$v['sys_name']);
                    }
                    if(file_exists($dir.'/IMG/AUDIO/'.$name)) {
                        unlink($dir.'/IMG/AUDIO/'.$name);
                    }
                }
            }
        }
    }
}