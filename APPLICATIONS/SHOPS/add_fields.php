<?php
$ADDED_FIELDS = [
    0 => [
        'VALUE'=>'',
        'field_type'=>'float',
        'name'=>'Широта',
        'types'=>'float',
        'visible'=>0,
        'block'=>1,
    ],
    1 => [
        'VALUE'=>'',
        'field_type'=>'float',
        'name'=>'Долгота',
        'types'=>'float',
        'visible'=>0,
        'block'=>1,
    ],
    2 => [
        'VALUE'=>'',
        'field_type'=>'int',
        'name'=>'IDcity',
        'types'=>'int',
        'visible'=>0,
        'block'=>1,
    ],
    3 => [
        'VALUE'=>'',
        'field_type'=>'int',
        'name'=>'IDcountry',
        'types'=>'int',
        'visible'=>0,
        'block'=>1,
    ],
    4 => [
        'VALUE'=>'',
        'field_type'=>'list',
        'name'=>'Телефон заказа',
        'types'=>'text',
        'visible'=>1,
        'block'=>0,
        'fixed'=>1,
    ],
    5 => [
        'VALUE'=>'0',
        'field_type'=>'int',
        'name'=>'Скидка %',
        'types'=>'int',
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