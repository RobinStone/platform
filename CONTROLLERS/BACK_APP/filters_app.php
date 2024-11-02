<?php
if(Access::scanLevel() < 6) {
    error('Низкий уровень допуска');
}

switch($_POST['com']) {
    case 'test':
        $err = isset_columns($_POST, ['']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'add_last_filter':
        $err = isset_columns($_POST, ['name', 'id', 'type']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $access_types = ['main', 'under', 'list'];
        if(!in_array($post['type'], $access_types)) {
            error('Ошибка типа');
        }
        $filters = SITE::$profil->get_attachment('last-filters');
        if(!is_array($filters)) {
            $filters = [];
        }
        if(!empty($filters)) {
            $key = array_key_last($filters);
            if($filters[$key]['type'] !== $post['type'] || $filters[$key]['id'] !== $post['id']) {
                foreach($filters as $k=>$v) {
                    if($v['name'] === $post['name'] && $v['id'] === $post['id'] && $v['type'] === $post['type']) {
                        unset($filters[$k]);
                    }
                }
                $filters[] = [
                    'name' => $post['name'],
                    'type' => $post['type'],
                    'id' => $post['id']
                ];
            }
        } else {
            $filters[] = [
                'name' => $post['name'],
                'type' => $post['type'],
                'id' => $post['id']
            ];
        }
        if(count($filters) > 10) {
            array_shift($filters);
        }
        SITE::$profil->set_attachment('last-filters', $filters);
        ans('ok');
        break;
    case 'get_last_filters':
        $filters = SITE::$profil->get_attachment('last-filters');
        if(!is_array($filters)) {
            $filters = [];
        } else {
            $filters = array_reverse($filters);
        }
        ans('ok', $filters);
        break;
    case 'delete_my_pressets':
        $err = isset_columns($_POST, ['folder']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $prr = SITE::$profil->get_attachment('pressets.'.$post['folder']);
        if($prr !== '') {
            SITE::$profil->delete_attachment('pressets.'.$post['folder']);
        }
        ans('ok');
        break;
    case 'delete_my_filters':
        SITE::$profil->delete_attachment('last-filters');
        ans('ok');
        break;
    case 'del_param':
        $err = isset_columns($_POST, ['id', 'folder']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $prr = SITE::$profil->get_attachment('pressets');
        if($prr !== '') {
            if(isset($prr[$post['folder']])) {
                $key = array_search($post['id'], $prr[$post['folder']]);
                if($key !== false) {
                    unset($prr[$post['folder']][$key]);
                    SITE::$profil->set_attachment('pressets', $prr);
                }

            }
        }
        ans('ok');
        break;
    case 'get_my_presets':
        $rows = SITE::$profil->get_attachment('pressets');
        $all = [];
        if(is_array($rows)) {
            foreach($rows as $item) {
                $all = array_merge($all, $item);
            }
            $all = array_unique($all);
            if(!empty($all)) {
                $filters = SQL_ROWS_FIELD(q("SELECT id, field_name, field FROM filters WHERE id IN (".implode(',',$all).")"), 'id');
                $rows['_all'] = $filters;
            } else {
                $rows['_all'] = [];
            }
        }
        ans('ok', $rows);
        break;
    case 'add_param_in_presets':
        $err = isset_columns($_POST, ['id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $id = (int)$post['id'];
        $prr = SITE::$profil->get_attachment('pressets');
        if($prr === '') {
            $prr = [
                'flows'=>[$id],
                'importants'=>[],
            ];
            SITE::$profil->set_attachment('pressets', $prr);
        } else {
            if(isset($prr['flows'])) {
                if(!in_array($id, $prr['flows'])) {
                    $prr['flows'][] = $id;
                    SITE::$profil->set_attachment('pressets', $prr);
                }
            } else {
                SITE::$profil->set_attachment('pressets.flows', [$id]);
            }
        }
        ans('ok', $prr);
        break;
    case 'update_filter':
        $err = isset_columns($_POST, ['arr']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        if((int)$post['arr']['id'] === -1) {
            if($id = FILTERS::create_new_from_json($post['arr'])) {
                ans('ok', $id);
            }
        } else {
            if($id = FILTERS::update_filter_from_json($post['arr'])) {
                ans('ok', $id);
            }
        }
        error('Произошла непредвиденная ошибка в процессе создания нового параметра для фильтра...');
        break;
    case 'get_prefabs':
        $rows = SQL_ROWS_FIELD(q("SELECT * FROM presets"), 'name');
        foreach($rows as &$item) {
            $item['json'] = unserialize($item['json']);
        }
        ans('ok', $rows);
        break;
    case 'get_filter_param':
        $err = isset_columns($_POST, ['id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        if($row = SQL_ONE_ROW(q("SELECT * FROM filters WHERE id=".(int)$post['id']))) {
            $value = $row['default'];
            if (is_null($value)) {
                $row['default'] = NULL;
            } elseif (is_string($value)) {
                $unserialized = @unserialize($value);
                if ($unserialized !== false || $value === 'b:0;') {
                    $row['default'] = $unserialized;
                } else {
                    $row['default'] = $value;
                }
            } else {
                $row['default'] = '-';
            }
            ans('ok', $row);
        }
        error('Не найден параметр по такому ID...');
        break;
    case 'get_filter':
        $err = isset_columns($_POST, ['type', 'id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        switch($post['type']) {
            case 'main':
                if($ans = SQL_ONE_FIELD(q("SELECT json FROM shops_categorys WHERE id=".(int)$post['id']))) {
                    ans('ok', unserialize($ans));
                } else {
                    ans('ok', $ans);
                }
                break;
            case 'under':
                if($ans = SQL_ONE_FIELD(q("SELECT json FROM shops_undercats WHERE id=".(int)$post['id']))) {
                    ans('ok', unserialize($ans));
                } else {
                    ans('ok', $ans);
                }
                break;
            case 'list':
                if($ans = SQL_ONE_FIELD(q("SELECT json FROM shops_lists WHERE id=".(int)$post['id']))) {
                    ans('ok', unserialize($ans));
                } else {
                    ans('ok', $ans);
                }
                break;
        }
        error('Ошибка типа. Тип - не распознан...');
        break;
    case 'delete_filter':
        $err = isset_columns($_POST, ['id', 'type']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        switch($post['type']) {
            case 'main':
                q("UPDATE shops_categorys SET json=NULL WHERE id=".(int)$post['id']);
                ans('ok');
                break;
            case 'under':
                q("UPDATE shops_undercats SET json=NULL WHERE id=".(int)$post['id']);
                ans('ok');
                break;
            case 'list':
                q("UPDATE shops_lists SET json=NULL WHERE id=".(int)$post['id']);
                ans('ok');
                break;
            default:
                $type = Access::get_type_field($post['type'], 'json');
                if($type === 'text') {
                    q("UPDATE ".db_secur($post['type'])." SET json=NULL WHERE id=".(int)$post['id']);
                    ans('ok');
                }
                break;
        }
        error('Ошибка типа...');
        break;
    case 'get_main':
        INCLUDE_CLASS('shops', 'cataloger');
        $rows = CATALOGER::INIT()->main_cats;
        $ans = [];
        foreach($rows as $item) {
            $ans[$item['category']] = [
                'id'=>$item['id'],
                'logo'=>$item['logo']
            ];
        }
        ksort($ans);

        $ids = array_column($ans, 'id');
        if(count($ids) <= 0) {
            error('Категории - ОТСУТСТВУЮТ...');
        }

        $filters = SQL_ROWS_FIELD(q("
            SELECT id, json FROM shops_categorys 
            WHERE id IN(".implode(',',$ids).") AND
            json <> ''
            "
        ), 'id');

        foreach($ans as &$item) {
            if(isset($filters[$item['id']])) {
                $item['json'] = $filters[$item['id']];
            }
        }

        ans('ok', $ans);
        break;
    case 'get_under':
        $err = isset_columns($_POST, ['id_main_cat']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        INCLUDE_CLASS('shops', 'cataloger');
        $rows = CATALOGER::INIT()->get_all_under_cats($post['id_main_cat']);
        $ans = [];
        foreach($rows as $item) {
            $ans[$item['under_cat']] = [
                'id'=>$item['id'],
                'logo'=>$item['logo']
            ];
            if($item['logo'] === '-') {
                $ans[$item['under_cat']]['logo'] = '20230508-154105_id-2-312764.svg';
            }
        }
        ksort($ans);

        $ids = array_column($ans, 'id');
        if(count($ids) <= 0) {
            error('Под-категории - ОТСУТСТВУЮТ...');
        }

        $filters = SQL_ROWS_FIELD(q("
            SELECT id, json FROM shops_undercats 
            WHERE id IN(".implode(',',$ids).") AND
            json <> ''
            "
        ), 'id');
        foreach($ans as &$item) {
            if(isset($filters[$item['id']])) {
                $item['json'] = $filters[$item['id']];
            }
        }

        ans('ok', $ans);
        break;
    case 'get_action_list':
        $err = isset_columns($_POST, ['id_under_cat']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        INCLUDE_CLASS('shops', 'cataloger');
        $rows = CATALOGER::INIT()->get_all_action_list(-1, $post['id_under_cat']);

        $ans = [];
        foreach($rows as $item) {
            $ans[$item['lists']] = [
                'id'=>$item['id'],
                'logo'=>$item['logo']
            ];
            if($item['logo'] === '-') {
                $ans[$item['lists']]['logo'] = '20230508-154105_id-2-312764.svg';
            }
        }
        ksort($ans);

        $ids = array_column($ans, 'id');
        if(count($ids) <= 0) {
            error('Активные листы - ОТСУТСТВУЮТ...');
        }

        $filters = SQL_ROWS_FIELD(q("
            SELECT id, json FROM shops_lists 
            WHERE id IN(".implode(',',$ids).") AND
            json <> ''
            "
        ), 'id');
        foreach($ans as &$item) {
            if(isset($filters[$item['id']])) {
                $item['json'] = $filters[$item['id']];
            }
        }

        ans('ok', $ans);
        break;
    case 'get_filters':
        $err = isset_columns($_POST, ['txt']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $rows = SQL_ROWS_FIELD(q("
        SELECT field_name, id, alias 
        FROM filters 
        WHERE 
        field_name LIKE '%".db_secur($post['txt'])."%' OR
        alias LIKE '%".db_secur($post['txt'])."%'
        LIMIT 15
        "), 'field_name');
        ans('ok', $rows);
        break;
}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);
