<?php
switch($_POST['com']) {
    case 'test':
        $err = isset_columns($_POST, ['user_id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        echo 'Тут находится тестовое сообщение для настройки информационнфх панелей на несколько строк';
        break;
    case 'save_preset':
        $err = isset_columns($_POST, ['table', 'column', 'id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $count = 0;
        switch($post['table']) {
            case 'shops_categorys':
                $count = count(FILTERS::update_filter_item_group($post['id'], PRODUCT_GROUP::MAIN_CAT));
                break;
            case 'shops_undercats':
                $count = FILTERS::update_filter_item_group($post['id'], PRODUCT_GROUP::UNDER_CAT);
                break;
            case 'shops_lists':
                $count = FILTERS::update_filter_item_group($post['id'], PRODUCT_GROUP::ACTION_LIST);
                break;
            default:
                error('Пресет можно добавить только для товаров');
                break;
        }
        if($count > 0) {
            ans('Было добавлено новых пресетов - '.$count);
        } else {
            ans('Ни одного нового пресета добавлено не было.');
        }
        break;
    case 'get_filter_item':
        $err = isset_columns($_POST, ['id']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        echo json_encode(FILTERS::get_json_from_filters_hub($post['id']), JSON_UNESCAPED_UNICODE);
        exit;
        break;
    case 'get_preset_list':
        $err = isset_columns($_POST, ['txt']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        $rows = FILTERS::find_filter_preset($post['txt']);
        ob_start();
        foreach($rows as $k=>$v) {
            echo "<option data-value='".$v['alias']."&nbsp;&nbsp;&nbsp;&nbsp;".$v['field']."&nbsp;&nbsp;&nbsp;&nbsp;".$v['field_name']."' data-id='".$v['id']."'>".$v['alias']."&nbsp;&nbsp;&nbsp;&nbsp;".$v['field']."&nbsp;&nbsp;&nbsp;&nbsp;".$v['field_name']."</option>";
        }
        ans(ob_get_clean());
        break;
    case 'wtf':
        $err = isset_columns($_POST, ['wtf']);
        if(is_array($err)) {
            error('Отсутствуют следующие поля: ', $err);
        }
        ob_start();
        wtf(json_decode($post['wtf'], true), 1);
        ans(ob_get_clean());
        break;


}
echo json_encode($ans, JSON_UNESCAPED_UNICODE);

