<?
$footer = SUBD::getLineDB('mailer', 'name', 'Футер')['html'];
?>

<footer class="footer-wrapper">
<?php f_('footer', './TEMPLATES/footer.php'); ?>
<ul class="flex between">
    <li>
        <b>Электронная почта: </b>info@labuton.ru<br>
        Наш тел. звонок <b style="color: red">бесплатный</b> по России<br>
        <a href="tel:+79233031488">отдел контроля качества: +79233031488</a><br>
        Юридические данные:<br>
        ИП Насиров Камран Рамиз оглы<br>
        ОГРНИП 320246800036410<br>
    </li>
    <li>Правый блок</li>
</ul>

<?php f_end('footer'); ?>
</footer>

<?php

echo render('auth-form');

//if (count(Core::$JS) > 0) {
//    echo implode("\r\n", Core::$JS);
//}
Message::render();
ERRS::render();
?>

</main>
</body>
</html>