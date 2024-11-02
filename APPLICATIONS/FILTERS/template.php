<section class="shop-wrapp">
    <div class="paddinger-5px">Данное приложение позволяет задавать и изменять фильтры товаров.</div>
    <div class="flex between gap-10" style="min-height: 32px; max-width: 400px">
        <button class="btn-gray btn-gray-text not-border micro-btn padding-btn" onclick="sel_mask()">ВЫБРАТЬ ФИЛЬТР</button>
        <button class="btn-gray btn-gray-text not-border micro-btn padding-btn" onclick="info_info(undefined, 'Суть работы фильтров: фильтры работают по схеме пере-подчинения от главной категории до ' +
     'активных листов при чём, активные листы имеют самый высокий приоритет над остальными.<br><br>' +
      'Скажем, если в главной категории ТРАНСПОРТ будет установлен блок с фильтрами, ' +
       'то этот блок будет показан всем его подкатегориям и активным листам, ' +
        'но если какая то подкатегория имеет свой собственный блок, то он будет показан ' +
         'в приоритетном порядке, при чём блок категории, в этом случае, - будет проигнорирован.', 'Инструкция')">❓</button>
        <button class="btn-gray btn-gray-text not-border micro-btn padding-btn" onclick="open_table('filters')">Посмотреть таблицу фильтров</button>
    </div>
    <div style="margin-bottom: 10px">
        <details class="details my-last-filters">
            <summary onclick="open_my_filters($(this).parent())">
                <span>Последние сборки фильтров</span>
                <button onclick="clear_all_my_filters()" class="btn-gray btn-gray-text not-border micro-btn padding-btn">Очистить всё</button>
            </summary>
        </details>
    </div>
    <?php HTML::line(12, 10, '#00000066'); ?>
    <div>
        <details class="details my-presets">
            <summary onclick="open_my_presets($(this).parent())">
                <span>Мои пресеты</span>
                <button onclick="clear_all_my_pressets()" class="btn-gray btn-gray-text not-border micro-btn padding-btn">Очистить всё</button>
            </summary>
        </details>
    </div>
    <div class="flex between gap-10 search-block">
        <fieldset class="fieldset">
            <legend style="text-align: right">Поиск или создание нового прессета</legend>
            <div class="text-input-wrapper flex gap-10 between">
                <input onchange="selected_input(this)" oninput="find_or_create(this)" list="filters" type="text">
                <button onclick="selected_input($(this).closest('div').find('input'))" class="btn-gray btn-gray-text not-border micro-btn padding-btn">Ok</button>
                <button onclick="$(this).closest('div').find('input').val('')" class="btn-gray btn-gray-text not-border micro-btn padding-btn">✕</button>
            </div>
        </fieldset>
    </div>
</section>

<datalist id="filters" class="filters-data"></datalist>

<template id="editor-filter">
    <div class="editor-item-filter">
        <table>
            <tr>
                <fieldset class="flex between gap-15 fieldset param-names">
                    <input data-name="param_name" oninput="enter_name(this)" type="text" placeholder="Название параметра">
                    <input data-name="param_name_alias" oninput="enter_name(this, true)" style="background-color: #cecece" type="text" placeholder="ID параметра">
                </fieldset>
            </tr>
            <tr>
                <div class="flex gap-10" style="margin-top: 10px">
                    <fieldset class="fieldset min-size" style="max-width: 275px">
                        <legend>Видимость поля</legend>
                        <?php HTML::toggler('visible', 'Видимое', 'Скрытое', 'float-left margin-right-10', '', 1, true); ?>
                        <div class="micro-text" style="text-align: right">
                            Параметр предназначен для каких-то скрытых параметров. Которые быть должны, но от отображения в фильтре - скрыты.
                        </div>
                    </fieldset>
                    <fieldset class="fieldset min-size" style="width: 133px">
                        <legend>Обязательное для заполнения</legend>
                        <?php HTML::toggler('required', 'Да', 'Нет', '', '', 0); ?>
                    </fieldset>
                    <fieldset class="fieldset min-size params-type">
                        <legend>Тип элемента параметра</legend>
                        <select data-name="type_element" onchange="change_type_filter_item(this)">
                            <option>-</option>
                            <option>Поле ввода</option>
                            <option>Текстовый блок</option>
                            <option>Переключатель (ДА/НЕТ)</option>
                            <option>Список</option>
                        </select>
                        <div class="flex between gap-10 align-center">
                            <?php HTML::toggler('type-input', 'Строка','Число','disabled'); ?>
                            <button onclick="show_types()" class="btn-gray btn-gray-text not-border micro-btn padding-btn" style="margin-top: 7px">❓</button>
                        </div>
                    </fieldset>
                </div>
            </tr>
            <tr>
                <fieldset class="fieldset flex json" style="gap: 5px; max-width: 640px; margin-top: 10px">
                    <div class="left-panel"></div>
                    <div class="right-panel"></div>
                </fieldset>
            </tr>
            <tr>
                <div class="flex" style="justify-content: right; margin-top: 10px; padding-right: 20px">
                    <button onclick="filter_parameter_update(this)" class="save-all-btn btn-gray btn-gray-text not-border micro-btn padding-btn">Сохранить</button>
                </div>
            </tr>
        </table>
    </div>
</template>

<template id="tmp-input">
    <div class="tmp tmp-input">
        <button onclick="change_default_string(this)" class="btn-gray btn-gray-text not-border micro-btn padding-btn">Изменить "По умолчанию"</button>
    </div>
</template>
<template id="tmp-text">
    <div class="tmp tmp-text">
        <button onclick="change_default_text(this)" class="btn-gray btn-gray-text not-border micro-btn padding-btn">Изменить "По умолчанию"</button>
    </div>
</template>
<template id="tmp-bool">
    <div class="tmp tmp-bool">
        <button onclick="change_bool_list(this)" class="btn-gray btn-gray-text not-border micro-btn padding-btn">Состав ДА / НЕТ</button>
        <div class="flex between">
            <?php HTML::toggler('preset', 'сост. 0:', 'сост. 1:', '', 'change_stat(this)', 1); ?>
        </div>
        <div style="margin-top: 10px" class="micro-text">Внимание! Переменная <b style="background-color: yellow">"preset"</b> указывает не на индекс <b style="background-color: yellow">"states"</b>, а именно на порядок, начиная с 1-цы</div>
    </div>
</template>
<template id="tmp-list">
    <div class="tmp tmp-list">
        <button onclick="change_list_items(this)" class="btn-gray btn-gray-text not-border micro-btn padding-btn">Состав списка</button>
    </div>
</template>