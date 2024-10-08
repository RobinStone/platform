<?php
return [
    'Изображение'=>[
        'id'=>1,
        'alias'=>'images',
        'order'=>10,
        'type'=>'string',
        'field'=>'image',
        'filter'=>0,
        'column'=>'',
        'default'=>'none.svg',
        'block'=>1,
        'visible'=>1,
        'required'=>1,
        'field_name'=>'Изображение',
    ],
    'Название товара'=>[
        'id'=>2,
        'alias'=>'product_name',
        'order'=>20,
        'type'=>'string',
        'field'=>'input',
        'filter'=>0,
        'column'=>'name',
        'default'=>'',
        'block'=>1,
        'visible'=>1,
        'required'=>1,
        'field_name'=>'Название товара',
    ],
    'В наличии'=>[
        'id'=>17,
        'alias'=>'active',
        'order'=>24,
        'type'=>'bool',
        'field'=>'bool',
        'column'=>'',
        'default'=>[
            'states'=>[
                'Есть', 'Нету'
            ],
            'preset'=>1,
        ],
        'block'=>1,
        'visible'=>1,
        'required'=>0,
        'field_name'=>'В наличии',
    ],
    'Стоимость'=>[
        'id'=>3,
        'alias'=>'price',
        'order'=>30,
        'type'=>'float',
        'field'=>'input',
        'column'=>'price',
        'default'=>'',
        'block'=>1,
        'visible'=>1,
        'required'=>1,
        'field_name'=>'Стоимость',
    ],
    'Расположение'=>[
        'id'=>4,
        'alias'=>'place',
        'order'=>40,
        'type'=>'string',
        'field'=>'input-object-place',
        'filter'=>0,
        'column'=>'',
        'default'=>'',
        'block'=>1,
        'visible'=>1,
        'required'=>1,
        'field_name'=>'Расположение',
    ],
    'Тип (категория)'=>[
        'id'=>5,
        'alias'=>'category',
        'order'=>50,
        'type'=>'int',
        'field'=>'input-object-cat',
        'filter'=>0,
        'column'=>'shops_categorys',
        'default'=>-1,
        'block'=>1,
        'disabled'=>0,
        'visible'=>1,
        'required'=>1,
        'field_name'=>'Тип (категория)',
    ],
    'Подкатегория'=>[
        'id'=>6,
        'alias'=>'under-cat',
        'order'=>60,
        'type'=>'int',
        'field'=>'input-object-undercat',
        'filter'=>0,
        'column'=>'shops_undercats',
        'default'=>-1,
        'block'=>1,
        'disabled'=>1,
        'visible'=>1,
        'required'=>0,
        'field_name'=>'Подкатегория',
    ],
    'Множество'=>[
        'id'=>7,
        'alias'=>'action-list',
        'order'=>70,
        'type'=>'int',
        'field'=>'input-object-actionlist',
        'filter'=>0,
        'column'=>'shops_lists',
        'default'=>-1,
        'block'=>1,
        'disabled'=>1,
        'visible'=>1,
        'required'=>0,
        'field_name'=>'Множество',
    ],
    'Количество'=>[
        'id'=>8,
        'alias'=>'count',
        'order'=>80,
        'type'=>'int',
        'field'=>'input-object-counter',
        'filter'=>0,
        'column'=>'count',
        'default'=>-1,
        'block'=>1,
        'visible'=>1,
        'required'=>1,
        'field_name'=>'Количество',
    ],
    'Описание'=>[
        'id'=>9,
        'alias'=>'descr',
        'order'=>90,
        'type'=>'text',
        'field'=>'tiny',
        'filter'=>0,
        'column'=>'',
        'default'=>'',
        'block'=>1,
        'visible'=>1,
        'required'=>1,
        'field_name'=>'Описание',
    ],
    'Широта'=>[
        'id'=>10,
        'alias'=>'latitude',
        'order'=>100,
        'type'=>'float',
        'field'=>'input',
        'filter'=>0,
        'column'=>'',
        'default'=>'',
        'block'=>0,
        'visible'=>0,
        'required'=>1,
        'field_name'=>'Широта',
    ],
    'Долгота'=>[
        'id'=>11,
        'alias'=>'longitude',
        'order'=>110,
        'type'=>'float',
        'field'=>'input',
        'filter'=>0,
        'column'=>'',
        'default'=>'',
        'block'=>0,
        'visible'=>0,
        'required'=>1,
        'field_name'=>'Долгота',
    ],
    'IDcity'=>[
        'id'=>12,
        'alias'=>'city_id',
        'order'=>120,
        'type'=>'int',
        'field'=>'input',
        'filter'=>0,
        'column'=>'city_id',
        'default'=>-1,
        'block'=>0,
        'visible'=>0,
        'required'=>1,
        'field_name'=>'IDcity',
    ],
    'IDcountry'=>[
        'id'=>13,
        'alias'=>'country_id',
        'order'=>130,
        'type'=>'int',
        'field'=>'input',
        'filter'=>0,
        'column'=>'',
        'default'=>-1,
        'block'=>0,
        'visible'=>0,
        'required'=>1,
        'field_name'=>'IDcountry',
    ],
    'Телефон заказа'=>[
        'id'=>14,
        'alias'=>'phone',
        'order'=>140,
        'type'=>'string',
        'field'=>'list',
        'filter'=>0,
        'column'=>'',
        'default'=>['НЕТ'],
        'block'=>1,
        'visible'=>1,
        'required'=>1,
        'field_name'=>'Телефон заказа',
    ],
    'Скидка %'=>[
        'id'=>15,
        'alias'=>'discount',
        'order'=>150,
        'type'=>'int',
        'field'=>'input',
        'column'=>'',
        'default'=>0,
        'block'=>1,
        'visible'=>1,
        'required'=>0,
        'field_name'=>'Скидка %',
    ],
];