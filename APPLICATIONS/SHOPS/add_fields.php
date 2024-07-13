<?php
$ADDED_FIELDS = [
    0 => [
        'VALUE'=>'',
        'field_type'=>'number',
        'name'=>'Широта',
        'types'=>'val_float',
        'visible'=>0,
        'block'=>1,
    ],
    1 => [
        'VALUE'=>'',
        'field_type'=>'number',
        'name'=>'Долгота',
        'types'=>'val_float',
        'visible'=>0,
        'block'=>1,
    ],
    2 => [
        'VALUE'=>'',
        'field_type'=>'number',
        'name'=>'IDcity',
        'types'=>'val_int',
        'visible'=>0,
        'block'=>1,
    ],
    3 => [
        'VALUE'=>'',
        'field_type'=>'number',
        'name'=>'IDcountry',
        'types'=>'val_int',
        'visible'=>0,
        'block'=>1,
    ],
    4 => [
        'VALUE'=>'',
        'field_type'=>'list',
        'name'=>'Телефон заказа',
        'types'=>'val_text',
        'visible'=>1,
        'block'=>0,
        'fixed'=>1,
    ],
    5 => [
        'VALUE'=>'0',
        'field_type'=>'number',
        'name'=>'Скидка %',
        'types'=>'val_int',
        'visible'=>1,
        'block'=>0,
        'fixed'=>1,
    ],
    6 => [
        'VALUE'=>'Не выбрано',
        'title'=>'Укажите состояние товара',
        'field_type'=>'list',
        'name'=>'Состояние товара',
        'types'=>'val_text',
        'visible'=>1,
        'block'=>0,
        'fixed'=>1,
        'variants'=> [
            0=>'Не выбрано',
            1=>'Новое',
            2=>'Б/у',
        ]
    ],
];