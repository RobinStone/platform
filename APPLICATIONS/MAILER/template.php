<?php
$ask = q("SELECT mailer.name FROM `mailer`");
$MAILER = SQL_ROWS($ask);
?>
<section class="mailer">
    <div class="header-text">Mailer - BUILDER</div>
    <div class="auto-mailer visually-hidden">
        <h4>Авто-рассылка писем</h4>
        <details>
            <summary><span>Панель загрузки</span><span>▼</span></summary>
            <div class="flex column">
                <textarea id="emails-list" style="margin-bottom: 10px" rows="10"></textarea>
                <button style="align-self: flex-start" onclick="begin_sending()">Запустить авторассылку</button>
            </div>
        </details>
        <details>
            <summary><span id="queue-count">В очереди (0)</span><span>▼</span></summary>
            <ul class="list-queue"></ul>
        </details>
        <details>
            <summary><span id="ok-count">Успешно (0)</span><span>▼</span></summary>
            <ul class="ok-queue"></ul>
        </details>
        <details>
            <summary><span id="error-count">Ошибки (0)</span><span>▼</span></summary>
            <ul class="error-queue"></ul>
        </details>
    </div>
    <div id="item-4"></div>
    <div class="sellector flex">
        <select onchange="load_mail(this)">
            <option>Выберете шаблон</option>
            <? foreach($MAILER as $v) { ?>
                <option><?=$v['name']?></option>
            <? } ?>
        </select>
        <div class="flex between template-controller" style="margin-left: 15px">
            <button id="save-btn" class="action-btn" title="Сохранить этот шаблон" onclick="save()"><?=RBS::SVG('20230622-231313_id-2-613874.svg')?></button>
            <button class="action-btn" title="Обновить шаблон из БД" onclick="update_template()"><?=RBS::SVG('20230618-232141_id-2-245837.svg')?></button>
            <button class="action-btn" title="Создать копию этого шаблона" onclick="copyd()"><?=RBS::SVG('20230508-134232_id-2-256239.svg')?></button>
            <button class="action-btn" title="Полностью удалить этот шаблон" onclick="deller()"><?=RBS::SVG('20230614-090714_id-2-993962.svg')?></button>
            <button class="action-btn" title="Отправить шаблон по почте" onclick="send()"><?=RBS::SVG('20230803-132753_id-2-647524.svg')?></button>
            <button class="action-btn" title="Авто-рассылка" onclick="$('.auto-mailer').toggleClass('visually-hidden')">A</button>
            <button class="action-btn" title="Трансформировать письмо с учётом переменных" onclick="transform_mail()"><?=RBS::SVG('20230505-205557_id-2-685693.svg')?></button>
            <button class="action-btn" title="Откатить изменение" onclick="backer()"><?=RBS::SVG('20230618-232141_id-2-245837.svg')?></button>
            <button class="action-btn" title="Вперёд" onclick="forwarder()"><?=RBS::SVG('20230619-141850_id-2-168073.svg')?></button>
            <button class="action-btn" style="width: 60px" title="Буфер проверки решений" onclick="buffer_see()">LOG</button>
        </div>
    </div>

    <div id="mail" class="wrapper flex center mail-content"></div>
    <div class="log">
        <h4 class="flex between"><span>ЛОГ - просмотр задач</span>
            <div class="flex between">
                <button title="Сохранить ЛОГ" onclick="save_log()" style="color: #ffffff">✎</button>
                <button title="Очистить ЛОГ" onclick="log_clear()">💧</button>
                <button title="Обновить ЛОГ" onclick="refesh_log()" style="color: lime">↻</button>
                <button title="Скрыть" onclick="$('.log').removeClass('log-show')">❌</button>
            </div>
        </h4>
        <textarea id="ta"></textarea>
    </div>


</section>

<script>
    setTimeout(function() {
        dragElement($('.auto-mailer'));
        dragElement($('.log'));
    }, 100);
</script>
