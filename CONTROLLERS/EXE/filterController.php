<?phpinclude_CSS('product');include_CSS('profil');//include_JS('swipper');//include_CSS('swipper');include_JS('list_cards_product');include_JS('swipper_2');include_CSS('swipper_2');include_JS('checker-toggler');INCLUDE_CLASS('shops', 'shop');INCLUDE_CLASS('shops', 'cataloger');//$filtersAll = json_decode((SUBD::getJsonLineDB('filters', 'filters', 'all', false)));//wtf($filtersAll);$filter_name = $_COOKIE['filter_name'] ?? 'date';$cat_filter_matrix = [-1, -1, -1];$CAT = new CATALOGER();$count = [0,8];$filter = [        'self'=>[                'changed'=>'DESC',        ],];//wtf(Core::$ADMIN, 1);switch($filter_name) {    case 'place':        $filter = [            'params'=>[                'place'=>'ASC',            ]        ];//        $pr = DISTANCE::find_near_points();        break;    case 'price_min':        $filter = [            'params'=>[                    'price'=>'ASC',            ]        ];        break;    case 'price_max':        $filter = [                'params'=>[                        'price'=>'DESC',                ]        ];        break;    case 'price_from':        $filter = [                'params'=>[                        'price'=>'ASC',                ]        ];        break;    case 'price_to':        $filter = [                'params'=>[                        'price'=>'DESC',                ]        ];        break;}//t('one');//say(debug_backtrace());$menu_items = [];switch($type_page) {    case 'main_cat':        $items = [            $current['category'] => $current['cat_trans'],        ];        $before_addr = $current['cat_trans'].'/';        foreach($body_arr as $k=>$v) {            $menu_items[$k] = [                'ID'=>$v['id'],                'TYPE'=>'undercat',                'NAME'=>$v['under_cat'],                'TRANS'=>$v['undercat_trans'],                'LOGO_SVG'=>$v['logo'],                'LOGO_IMG'=>$v['logo_img'],                'COUNT_CHILD'=>count($v['ACTION_LIST'])            ];        }//        wtf($current);        $cat_filter_matrix[0] = (int)$current['id'];        $preview_products = SHOP::get_mix_products_at_all_shops(false, $count, -1, (int)$current['id'], -1, -1, [], true, $filter);//        wtf($preview_products, 1);//        wtf($filter, 1);//        wtf(SITE::$GEO,1);        if(isset($pr)) {            $preview_products = SHOP::get_products_list($pr);        }        break;    case 'under_cat':        $items = [            $main_cat['category'] => $main_cat['cat_trans'],            $current['under_cat'] => $current['undercat_trans'],        ];        $before_addr = $before_path;        foreach($body_arr as $k=>$v) {            $menu_items[$k] = [                'ID'=>$v['id'],                'TYPE'=>'actionlist',                'NAME'=>$v['lists'],                'TRANS'=>$v['translit'],                'LOGO_SVG'=>$v['logo'],                'LOGO_IMG'=>$v['logo_img'],                'COUNT_CHILD'=>-1            ];        }        $cat_filter_matrix[0] = (int)$main_cat['id'];        $cat_filter_matrix[1] = (int)$current['id'];        $preview_products = SHOP::get_mix_products_at_all_shops(false, $count, -1, (int)$main_cat['id'], $current['id'], -1, [], true, $filter);        break;    case 'action_list':        $items = [            $main_cat['category'] => $main_cat['cat_trans'],            $under_cat['under_cat'] => $under_cat['undercat_trans'],            $current['lists'] => $current['translit'],        ];        $before_addr = $before_path;        $cat_filter_matrix[0] = (int)$main_cat['id'];        $cat_filter_matrix[1] = (int)$under_cat['id'];        $cat_filter_matrix[2] = (int)$current['id'];        $preview_products = SHOP::get_mix_products_at_all_shops(false, $count, -1, (int)$main_cat['id'], $under_cat['id'], $current['id'], [], true, $filter);        break;}//foreach($preview_products as $k=>$v) {//    $preview_products[$k]['main_cat_trans'] = $CAT->id2main_cat($v['main_cat'], true);//    $preview_products[$k]['under_cat_trans'] = $CAT->id2under_cat($v['under_cat'], true);//    $preview_products[$k]['action_list_trans'] = $CAT->id2action_list($v['action_list'], true);//}//wtf($preview_products, 1);//wtf('robin', 1);//wtf($current, 1);//wtf($menu_items, 1);//wtf($cat_filter_matrix, 1);