<section class="seo">
    <div class="modul">
        <h2>robots.txt и sitemap.xml</h2>
        <div class="flex between gap-20">
            <div class="flex gap-15 between" style="width: fit-content">
                <button title="Загрузить на сервер файл с содержимым для robots.txt" onclick="$('#filer').attr('data-name', 'robots.txt'); $('#filer').click()" class="btn-gray btn-gray-text not-border padding-btn flex align-center gap-5"><img width="20" height="20" src="/DOWNLOAD/20230622-231313_id-2-613874.svg"> <b>robots.txt</b></button>
                <button title="Редактировать вручную файл robots.txt" onclick="save_robots()" class="btn-gray btn-gray-text not-border padding-btn flex align-center gap-5"><img width="20" height="20" src="/DOWNLOAD/20230621-212153_id-2-664482.svg"> <b>robots.txt</b></button>
            </div>
            <div class="vertical-line"></div>
            <div class="flex gap-15 between" style="width: fit-content">
                <button title="Загрузить на сервер файл с содержимым для sitemap.xml" onclick="$('#filer').attr('data-name', 'sitemap.xml'); $('#filer').click()" class="btn-gray btn-gray-text not-border padding-btn flex align-center gap-5"><img width="20" height="20" src="/DOWNLOAD/20230622-231313_id-2-613874.svg"> <b>sitemap.xml</b></button>
                <button title="Редактировать вручную файл sitemap.xml" onclick="save_xml()" class="btn-gray btn-gray-text not-border padding-btn flex align-center gap-5"><img width="20" height="20" src="/DOWNLOAD/20230621-212153_id-2-664482.svg"> <b>sitemap.xml</b></button>
            </div>
        </div>
    </div>
    <div class="modul">
        <h2>Шаблоны "title", "description", "keywords"</h2>
        <div class="flex gap-5">
            <div id="meta-box" tabindex="0" class="meta-box" contenteditable="false"></div>
            <div class="text-panel-control flex column gap-5">
                <button id="saver-btn" title="" onclick="save_meta()" class="btn-gray btn-gray-text not-border padding-btn align-center svg-wrapper rect-btn"><?=RBS::SVG('save')?></button>
                <button id="del-meta" title="Удалить блок мета-тегов для этой модели (все значения будут установлены 'по умолчанию')" onclick="del_meta()" class="btn-gray btn-gray-text not-border padding-btn align-center svg-wrapper rect-btn disabled"><?=RBS::SVG('garbage_2')?></button>
            </div>
        </div>
        <div class="meta-info"><b>Выберете модель</b> названия страницы<br>для которой необходимо изменить данные мета-тега</div>
        <div class="flex between">
            <button onclick="clear_meta()" class="btn-gray btn-gray-text not-border padding-btn align-center svg-wrapper closer-btn" style="width: 25px; height: 25px; margin-right: 10px"><?=RBS::SVG('krest')?></button>
            <input id="page-temp" placeholder="Выберете модель" type="text" list="pages" name="page">
            <div class="flex gap-5 disabled meta-selector">
                <button id="meta-btn-title" title="" onclick="get_constructor_write(this, 'title')" class="btn-gray btn-gray-text not-border padding-btn align-center">TITLE</button>
                <button id="meta-btn-descr" title="" onclick="get_constructor_write(this, 'description')" class="btn-gray btn-gray-text not-border padding-btn align-center">DESCRIPTION</button>
                <button id="meta-btn-keywords" title="" onclick="get_constructor_write(this, 'keywords')" class="btn-gray btn-gray-text not-border padding-btn align-center">KEYWORDS</button>
                <button id="meta-btn-h1" title="" onclick="get_constructor_write(this, 'h1')" class="btn-gray btn-gray-text not-border padding-btn align-center">H1</button>
            </div>
            <button title="Отобразить исходную таблицу мета-данных" onclick="open_table('pages');" class="btn-gray btn-gray-text not-border padding-btn flex align-center gap-5"><img width="20" height="20" src="/DOWNLOAD/20230505-204929_id-2-665695.svg"> <b>Таблица мета-данных</b></button>
        </div>
    </div>
</section>
<input id="filer" class="not-visible" type="file" accept=".txt,.xml">

<datalist id="pages">
    <? foreach($pages as $v) {
        $alias = $v['alias'];
        if($alias === '' || $alias === '-') {
            $alias = '++ '.$v['template'].' ++';
        }
        echo '<option data-id="'.$v['id'].'" data-val="'.$alias.'">'.$alias.'</option>';
    } ?>
</datalist>

<script>
    pages = <?=json_encode($pages, JSON_UNESCAPED_UNICODE)?>;
    commands = <?=json_encode($commands, JSON_UNESCAPED_UNICODE)?>;
    console.dir(pages);
    console.dir(commands);
</script>