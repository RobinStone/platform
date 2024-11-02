<?phpinclude_CSS_once('/TEMPLATES/CSS/table_card.css');include_JS_once('/TEMPLATES/JS/table_card.js');?><section class="table-card">    <table class="tb-card" data-id="<?=(int)$id_row?>" data-tbname="<?=$table_name?>">        <?php//        say($fields);        foreach($row as $k=>$v) {            $title = $k;            if(isset($fields[$k]) && $fields[$k]['column_title'] !== '-' && $fields[$k]['column_title'] !== '') {                $title = $fields[$k]['column_title'];            }            if($fields[$k]['showed'] == '1') {                $edited = '';                if($fields[$k]['edited'] == '1') {                    $edited = ' edited-field ';                }                echo '<tr class="'.$edited.'"><th data-title="' . $k . '">' . $title . '</th>';                switch ($fields[$k]['type']) {                    case 'file':                        if (!file_exists('./DOWNLOAD/' . $v)) {                            $v = '/IMG/SYS/img_not_exists.svg';                            $nm = $v;                            $op = " opacity ";                            $sizer = '';                            echo '<td style="position: relative;" data-type="' . $fields[$k]['type'] . '"><div class="input-img micro-img-wrapper"><img draggable="false" data-name="' . $nm . '" class="' . $op . '" width="100" height="100" src="' . $v . '">' . $sizer . '</div></td>';                        } else {                            switch(RBS::get_extention($v)) {                                case 'image':                                case 'svg':                                if (!file_exists('./DOWNLOAD/' . $v)) {                                    $v = '/IMG/SYS/img_not_exists.svg';                                    $nm = $v;                                    $op = " opacity ";                                    $sizer = '';                                    echo '<td style="position: relative;" data-type="' . $fields[$k]['type'] . '"><div class="input-img micro-img-wrapper"><img draggable="false" data-name="' . $nm . '" class="' . $op . '" width="100" height="100" src="' . $v . '">' . $sizer . '</div></td>';                                } else {                                    $nm = $v;                                    if (RBS::get_extention($v) !== 'svg') {                                        $v = '/IMG/img100x100/' . $v;                                        $editor = 'image';                                    } else {                                        $v = '/DOWNLOAD/' . $v . '?' . filemtime('./DOWNLOAD/' . $v);                                        $editor = 'svg';                                    }                                    $op = " buffer-file-dragger ";                                    $sizer = '<button class="sizer-btn" onclick="sizer($(this).parent().find(\'img\'), \'' . $editor . '\')"></button>';                                    echo '<td style="position: relative;" class="input-img" data-type="' . $fields[$k]['type'] . '"><div class="input-img micro-img-wrapper "><img draggable="true" data-name="' . $nm . '" class="' . $op . '" width="100" height="100" src="' . $v . '">' . $sizer . '</div></td>';                                }                                    break;                                case 'video':                                    echo '<td data-type="' . $fields[$k]['type'] . '">';                                    if (file_exists('./DOWNLOAD/' . $v)) {                                        $nm = explode('.', $v);                                        $name_file = $nm[count($nm) - 2];                                        $prev = "";                                        $fn = './IMG/VIDEO_PREVIEW/' . $name_file . '.jpg';                                        if (file_exists($fn)) {                                            $prev = "<img style='pointer-events: none' src='" . $fn . "?" . filemtime($fn) . "'>";                                        }                                        echo '<div draggable="true" data-name="' . $v . '" class="buffer-file-dragger micro-video-wrapper ">' . $prev . '<button class="sizer-btn sizer-video" onclick="sizer($(this).parent(), \'video\')"></button></div>';                                    } else {                                        echo '<i class="" style="font-weight: 100; opacity: 0.4">unknown file<br>' . $v . '</i>';                                    }                                    echo '</td>';                                    break;                                case 'audio':                                    echo '<td style="position: relative;" data-type="'.$fields[$k]['type'].'"><div draggable="true" data-name="' . $v . '" class="buffer-file-dragger audio-control "><audio controls="true" src="./DOWNLOAD/' . $v . '"></audio></div>';                                    break;                                case 'txt':                                    echo '<td style="position: relative;" data-type="'.$fields[$k]['type'].'"><div draggable="true" data-name="' . $v . "?" . filemtime("./DOWNLOAD/" . $v) . '" class="buffer-file-dragger "><img draggable="false" width="60" height="70" src="./IMG/SYS/txt.svg"><button style="left: 45px; top: 3px; right: unset" class="sizer-btn sizer-video" onclick="sizer($(this).closest(\'.buffer-file-dragger\'), \'txt\')"></button></div>';                                    break;                                case 'pdf':                                    echo '<td style="position: relative;" data-type="'.$fields[$k]['type'].'"><div draggable="true" data-name="' . $v . '" class="buffer-file-dragger "><img draggable="false" width="60" height="70" src="./IMG/SYS/pdf.svg"><button style="left: 45px; top: 3px; right: unset" class="sizer-btn sizer-video" onclick="sizer($(this).closest(\'.buffer-file-dragger\'), \'pdf\')"></button></div>';                                    break;                                case 'word':                                case 'tabs':                                    if (file_exists('./DOWNLOAD/' . $v)) {                                        switch (RBS::get_extention($v)) {                                            case 'word':                                                $fil = './IMG/SYS/word.svg';                                                break;                                            case 'tabs':                                                $fil = './IMG/SYS/tabs.svg';                                                break;                                        }                                        echo '<td style="position: relative;" data-type="'.$fields[$k]['type'].'"><div draggable="true" data-name="' . $v . '" class="buffer-file-dragger "><img draggable="false" width="60" height="70" src="' . $fil . '"><button style="left: 45px; top: 3px; right: unset" class="sizer-btn sizer-video" onclick="sizer($(this).closest(\'.buffer-file-dragger\'), \'word\')"></button></div>';                                    } else {                                        echo '<td style="position: relative;" data-type="'.$fields[$k]['type'].'"><i class="" style="font-weight: 100; opacity: 0.4">unknown file<br>' . $v . '</i>';                                    }                                    break;                                default:                                    echo '<td class="input-img " data-type="' . $fields[$k]['type'] . '">' . $v . '</td>';                                    break;                            }                        }                        break;                    case 'tinyint':                        if($v == '1') {                            echo '<td style="text-shadow: 0 0 2px lime" data-value="'.$v.'" data-true="'.$fields[$k]['params']['true'].'" data-false="'.$fields[$k]['params']['false'].'" data-type="' . $fields[$k]['type'] . '">' . $fields[$k]['params']['true'] . '</td>';                        } else {                            echo '<td style="text-shadow: 0 0 2px red" data-value="'.$v.'" data-true="'.$fields[$k]['params']['true'].'" data-false="'.$fields[$k]['params']['false'].'" data-type="' . $fields[$k]['type'] . '">' . $fields[$k]['params']['false'] . '</td>';                        }                        break;                    case 'select':                        echo '<td class="select-item" data-table-from="' . $fields[$k]['params']['table'] . '" data-column-from="' . $fields[$k]['params']['field'] . '" data-type="' . $fields[$k]['type'] . '">' . $v . '</td>';                        break;                    case 'enum':                        echo '<td class="select-item" data-list="'.implode('||', $fields[$k]['params']['list']).'" data-type="' . $fields[$k]['type'] . '">' . $v . '</td>';                        break;                    case 'datetime':                        echo '<td data-value="'.$v.'" data-type="' . $fields[$k]['type'] . '">' . date('d.m.Y H:i:s', strtotime($v)) . '</td>';                        break;                    case 'date':                        echo '<td data-value="'.$v.'" data-type="' . $fields[$k]['type'] . '">' . date('d.m.Y', strtotime($v)) . '</td>';                        break;                    case 'time':                        echo '<td data-value="'.$v.'" data-type="' . $fields[$k]['type'] . '">' . date('H:i:s', strtotime($v)) . '</td>';                        break;                    case 'text':                        if(!isset($fields[$k]['params']['type'])) {                            $fields[$k]['params'] = [];                            $fields[$k]['params']['type'] = 'text';                        }                        switch($fields[$k]['params']['type']) {                            case 'params':                            case 'json':                                echo '<td data-params="'.out_secur($v).'" class="y-scroller" data-type-text="'.$fields[$k]['params']['type'].'" data-type="' . $fields[$k]['type'] . '">';                                echo '<div class="card-table-wrapp"><table class="micro-table">';                                foreach(explode('|',$v) as $itm) {                                    $sh = explode('=',$itm);                                    if(count($sh) === 2) {                                        echo '<tr><td>'.$sh[0].'</td><td>'.$sh[1].'</td></tr>';                                    }                                }                                echo '</table></div></td>';                                break;                            default:                                echo '<td data-type="' . $fields[$k]['type'] . '">'.$v.'</td>';                                break;                        }                        break;                    default:                        echo '<td data-type="' . $fields[$k]['type'] . '">' . $v . '</td>';                        break;                }                echo '</tr>';            }        }        ?>    </table></section>