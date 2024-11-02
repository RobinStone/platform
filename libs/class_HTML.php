<?php
class HTML {
    /**
     * Возвращает HTML - элемент переключателя с настроенными параметрами
     *
     * @param string $name - имя компонента (для поиска его через js)
     * @param string $yes - надпись если в положении 1
     * @param string $no - надпись если в положении 0
     * @param array|string $classes - класс или классы для CSS
     * @param string $onclick_methtod - метод JS при переключении
     * @param int $preview_status - 0 или 1 начальное состояние

     * @return void
     */
    public static function toggler(string $name, string $yes='', string $no='', array|string $classes='', string $onclick_methtod='', int $preview_status=1, bool $traffic_light=false) {
        if($preview_status !== 1) {
            $preview_status = 0;
        }
        ob_start();
        if(is_array($classes)) {
            $classes = implode(" ", $classes);
        }
        if($traffic_light) {
            $classes .= " traffic-light ";
        }
        if($preview_status === 1) { $value = $yes; } else { $value = $no; }
        ?>
        <div <?php if(!empty($onclick_methtod)) { echo "onclick='".$onclick_methtod."'"; } ?>
                class="html-toggler <?=$classes?>"
                data-type="toggle"
                data-name="<?=$name?>"
                data-value="<?=$value?>"
                data-status="<?=$preview_status?>"
                data-value-yes="<?=$yes?>"
                data-value-no="<?=$no?>"
                >
            <div class="itms itm-1"><span class="unselect"><?=$yes?></span></div>
            <div class="itms itm-2"><span class="unselect"><?=$no?></span></div>
        </div>
        <?php echo ob_get_clean();
    }

    /**
     * Кнопка для вызова текстовой стравки
     *
     * @param string $txt
     * @param string $header
     * @param string $btn_ok
     * @param string $img_sys_name
     * @return void
     */
    public static function info_help_btn(string $txt, string $header='Информация', string $btn_ok='Ok', string $img_sys_name='') {
        ob_start(); ?>
        <button class="btn-gray btn-gray-text not-border micro-btn padding-btn" onclick="info_info(undefined, <?=$txt?>, '<?=$header?>', '<?=$btn_ok?>', '<?=$img_sys_name?>')">❓</button>
        <?php echo ob_get_clean();
    }

    /**
     * Горизонтальная разделительная линия
     *
     * @param int $margin_top
     * @param int $margin_bottom
     * @param string $color
     * @param int $line_height
     * @param LINE_TYPE $line_type
     * @return void
     */
    public static function line(int $margin_top=10, int $margin_bottom=10, string $color="#000000", int $line_height=1, LINE_TYPE $line_type=LINE_TYPE::SOLID) {
        echo "<div style='margin-top: {$margin_top}px; margin-bottom: {$margin_bottom}px; border-bottom: {$line_height}px {$line_type->value} {$color}'></div>";
    }

    /**
     * Все шаблоны хранятся в папке ./TEMPLATES/TEMP_HTML/...
     * @param string $temp_html_name
     */
    public static function MESS(string $temp_html_name) {
        $path = './TEMPLATES/TEMP_HTML/'.$temp_html_name.'.html';
        if(file_exists($path)) {
            echo file_get_contents($path);
        } else {
            echo 'no-content...';
        }
    }
}