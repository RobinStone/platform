<?php
class TOTALCOMANDER {
    public static function moove_items_to_folder($arr, $destination) {
        $destination = explode("/", $destination);
        if(isset($destination[0]) && $destination[0] === 'home') {
            unset($destination[0]);
        }
        $destination_last = end($destination);
        $destination = implode("/", $destination);
        $path = './APPLICATIONS/SHOPS/user_storages/'.Access::userID()."/";

        foreach($arr as $item) {
            if($item['type'] === 'D') {
                $arr = explode("/", $item['path']);
                $mooving_folder = end($arr);
                if($mooving_folder === 'SYSIMGS') {
                    break;
                }
                if(count(explode("/", $item['path'])) === 1 && $destination_last === '') {
                    break;
                }
                if($destination."/".$item['name'] === $item['path']) {
                    break;
                }
                if((strpos($destination, $item['path']) === 0 && $destination !== '') || $destination_last === $mooving_folder) {
                } else {
                    self::move_item($path.$item['path'], $path.$destination);
                }
            } else {
                self::move_item($path.$item['path'], $path.$destination);
            }
        }
        return true;
    }
    public static function delete_items(array $arr): bool
    {
        $path = './APPLICATIONS/SHOPS/user_storages/'.Access::userID()."/";
        foreach($arr as $v) {
            if($v['type'] === 'F') {
                unlink($path.$v['path']);
            } elseif($v['type'] === 'D') {
                self::delete_dir($path.$v['path']);
            }
        }
        return true;
    }
    public static function add_folder($path, $folder_name) {
        $path = trim($path, "/");
        $folder_name = mb_strtoupper($folder_name);
        $folder_name = trim($folder_name, " ");
        $path = explode('/', $path);
        if(count($path) > 0 && $path[0] === 'home') {
            unset($path[0]);
            $path = implode('/', $path);
            if($path === '') { $path = '/'; } else { $path = "/" . $path . "/"; }
            if(!is_dir('./APPLICATIONS/SHOPS/user_storages/'.Access::userID().$path.$folder_name)) {
                mkdir('./APPLICATIONS/SHOPS/user_storages/'.Access::userID().$path.$folder_name);
                ans('ok');
            }
            error('Директория с таким именем уже существует...');
        }
        error('Попытка доступа к запрещённой директории...');
    }
    public static function get_dir_catalog_of_user(string $path = "/"): bool|array
    {
        if(!is_dir('./APPLICATIONS/SHOPS/user_storages/'.Access::userID())) {
            mkdir('./APPLICATIONS/SHOPS/user_storages/'.Access::userID());
        }
        if($path !== '/' && $path !== "") {
            if($path[0] !== "/") {
                $path = "/".$path."/";
            }
        }
        if($path === "/SYSIMGS/") {
            $rows = SQL_ROWS(q("SELECT id, sys_name, user_name, created FROM file WHERE owner=".Access::userID()." AND type='image' "));
            $contents = [];
            foreach($rows as $v) {
                $contents[] = [
                    'SYSIMGS'=>1,
                    'name' => $v['user_name'],
                    'type' => 'F',
                    'size' => -1,
                    'created' => $v['created'],
                    'path' => $v['sys_name']
                ];
            }
            return $contents;
        }
        if(is_dir('./APPLICATIONS/SHOPS/user_storages/'.Access::userID().$path)) {
            $directoryPath = './APPLICATIONS/SHOPS/user_storages/'.Access::userID().$path;
            $contents = [];
            if($path === '/') {
                $contents[] = [
                    'name' => 'Системные изображения',
                    'type' => 'D',
                    'size' => -1,
                    'created' => '',
                    'path' => 'SYSIMGS'
                ];
            }
            if(is_dir($directoryPath)) {
                $items = scandir($directoryPath);
                $small_path = explode('/',$directoryPath);
                for($i=0;$i<=4;$i++) {
                    unset($small_path[$i]);
                }
                $small_path = implode('/',$small_path);
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }
                    $itemPath = $directoryPath . $item;
                    $itemStats = stat($itemPath);
                    $contents[] = [
                        'name' => $item,
                        'type' => is_dir($itemPath) ? 'D' : 'F',
                        'size' => $itemStats['size'],
                        'created' => date('Y-m-d H:i:s', $itemStats['ctime']),
                        'path' => $small_path.$item,
                        'info'=>mime_content_type($directoryPath.$item)
                    ];
                }
            }
            return $contents;
        } else {
            return false;
        }
    }

    public static function include_js_class_for_totalCommander() {
        ob_start();
        ?>
        <script>
            donor_folder = '';
            destination_folder = '';
            total = {
                left: [],
                right: []
            };
            class TOTALCOMMANDER {
                save(path, content, call_back=function () {}) {
                    BACK('total_commander', 'save', {path: path, content: content}, (mess) => {
                        mess_executer(mess, (mess) => {
                            this.update();
                            call_back();
                        });
                    });
                }
                /**
                 * Переместить файл / папку из одной папки в другую
                 * @param array_items
                 * @param folder_in
                 * @returns {boolean}
                 */
                moove(array_items, folder_in) {
                    if(destination_folder === 'home/SYSIMGS') {
                        say('В эту папку запрещено премещать что-либо...', 2);
                        return false;
                    }
                    if(donor_folder === 'home/SYSIMGS') {
                        say('Из этой папки запрещено премещать что-либо...', 2);
                        return false;
                    }
                    BACK('total_commander', 'moove_in', {arr: array_items, folder_in: folder_in}, (mess) => {
                        mess_executer(mess, (mess) => {
                            this.update();
                        });
                    });
                }
                /**
                 * Удаление массива элемнтов
                 * @param arr
                 */
                delete_items(arr) {
                    if(donor_folder === 'home/SYSIMGS') {
                        say('Запрещено удалять что-либо из этой папки', 2);
                        return false;
                    }
                    BACK('total_commander', 'del_items', {arr: arr}, (mess) => {
                        mess_executer(mess, (mess) => {
                            console.dir(mess);
                            if(typeof mess.params.errors !== 'undefined') {
                                say(mess.params.errors.join('<br>')+'<br>Нельзя удалить эти данные...', 2);
                            }
                            this.update();
                        });
                    });
                };

                /**
                 * Полное обновление командера
                 */
                update() {
                    this.update_panel();
                    this.update_panel('right');
                };
                /**
                 * Создание папки
                 * @param side может быть (left или right)
                 * @param name
                 */
                create_folder(side, name='') {
                    let path = this.get_path(side);
                    let exists = false;
                    name.toUpperCase();
                    if(donor_folder === 'home/SYSIMGS') {
                        say('В этой папке запрещено создавать свои папки...', 2);
                        return false;
                    }
                    $('.total-sides.'+side+'-side .t-row[data-type="folder"]').each(function(e, t) {
                        if(name === $(t).text()) {
                            exists = true;
                        }
                    });
                    if(exists) {
                        info_inputString(undefined, (mess) => {
                            this.create_folder(side, bufferText);
                        }, 'Папка с таким именем существует:', name);
                    } else {
                        BACK('total_commander', 'add_folder', {path: path, name: bufferText}, (mess) => {
                            mess_executer(mess, (mess) => {
                                this.update_panel(side);
                            });
                        });
                    }
                };
                update_panel(side='left') {
                    let path = this.get_path(side).split('/');
                    if(path[0] === 'home') {
                        path.shift();
                    }
                    path = path.join('/');
                    this.update_total_panel(path, side);
                }
                get_path(side) {
                    return $('.total-sides.' + side + '-side .bread-crumbs-total').text();
                };
                update_total(left = '', right = '') {
                    BACK('total_commander', 'update_total', {left: left, right: right}, (mess) => {
                        mess_executer(mess, (mess) => {
                            if(left !== '-') {
                                if(left === '/') {
                                    left = '';
                                } else {
                                    left = left.split('/').map(part => `<span>${part}</span>`).join('/');
                                }
                                $('.total-sides.left-side .bread-crumbs-total').html('<span>home</span>/' + left);
                                this.draw_side('left', mess.body.left);
                                total['left'] = mess.body.left;
                            }
                            if(right !== '-') {
                                if(right === '/') {
                                    right = '';
                                } else {
                                    right = right.split('/').map(part => `<span>${part}</span>`).join('/');
                                }
                                $('.total-sides.right-side .bread-crumbs-total').html('<span>home</span>/' + right);
                                this.draw_side('right', mess.body.right);
                                total['right'] = mess.body.right;
                            }
                        });
                    });
                };
                /**
                 *
                 * @param side - сторона (left / right)
                 * @param arr - массив получаемый из BACK
                 * @param mode - режим отображения (line / block)
                 */
                draw_side(side='left', arr=[], mode='line') {
                    let gallery = false;
                    if(this.get_path(side) === 'home/SYSIMGS') {
                        gallery = true;
                    }
                    let panel = $('.total-sides.'+side+'-side .total-content');
                    let size = 14;
                    panel.empty();
                    console.dir(arr);
                    for(let i in arr) {
                        size = 14;
                        let item = '';
                        let exp = '';
                        let ico = './DOWNLOAD/20230508-154105_id-2-312764.svg';
                        let file_type = 'undefined';
                        if(arr[i]['type'] === 'D') {
                            item = $('<div data-id="'+i+'" draggable="true" data-type="folder" class="t-row flex gap-5"><img width="14" height="14" src="./DOWNLOAD/dfcd917ff13c1783d3061279aac10b12.svg"><span><b class="count-lines-1">'+(arr[i]['name']).toUpperCase()+'</b></span></div>');
                        } else if(arr[i]['type'] === 'F') {
                            let lastDotIndex = arr[i]['name'].lastIndexOf('.');
                            if (lastDotIndex === -1) {}
                            exp = arr[i]['name'].substring(lastDotIndex + 1);
                            let data_file_name = '';
                            switch(exp.toLowerCase()) {
                                case 'docx':
                                case 'doc':
                                // case 'xlsx':
                                    file_type = 'word';
                                    data_file_name = arr[i]['path'];
                                    ico = './DOWNLOAD/20240817-113912_id-2-524291.svg';
                                    break;
                                case 'pdf':
                                    file_type = 'pdf';
                                    data_file_name = arr[i]['path'];
                                    ico = './DOWNLOAD/20240817-134701_id-2-462471.svg';
                                    break;
                                case 'xlsx':
                                case 'xlsm':
                                    file_type = 'table';
                                    data_file_name = arr[i]['path'];
                                    ico = './DOWNLOAD/20230505-204929_id-2-665695.svg';
                                    break;
                                case 'txt':
                                    file_type = 'text';
                                    data_file_name = arr[i]['path'];
                                    ico = './DOWNLOAD/a4e2f8736a5fab5ae02dab92e36a7143.svg'
                                    break;
                                case 'png':
                                case 'jpg':
                                case 'jpeg':
                                case 'gif':
                                case 'bmp':
                                case 'webp':
                                    file_type = 'image';
                                    if(gallery) {
                                        ico = "./IMG/img100x100/"+arr[i]['path'];
                                    } else {
                                        ico = "./APPLICATIONS/SHOPS/user_storages/<?=SITE::$user_id?>/"+arr[i]['path'];
                                    }
                                    size = 30;
                                    break;
                            }
                            let mg = '';
                            if(size > 14) {
                                mg = 'mg-5';
                            }
                            item = $('<div data-id="'+i+'" data-file-name="'+data_file_name+'" draggable="true" data-file-type="'+file_type+'" data-type="file" class="t-row flex gap-5 '+mg+'"><img width="'+size+'" height="'+size+'" src="'+ico+'"><span class="count-lines-1">'+arr[i]['name']+'</span></div>');
                        }
                        panel.append(item);
                    }
                };
                /**
                 * Обновить левую или правую панель командера
                 * @param path
                 * @param panel
                 */
                update_total_panel(path='/', panel='left') {
                    if(path === '') { path = '/'; }
                    if(panel === 'left') {
                        this.update_total(path, '-');
                    }
                    if(panel === 'right') {
                        this.update_total('-', path);
                    }
                };
            }
///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
            ctrl = false;
            document.addEventListener('keydown', function(event) {
                if (event.ctrlKey || event.metaKey) {
                    ctrl = true;
                }
            });
            document.addEventListener('keyup', function(event) {
                if (event.ctrlKey || event.metaKey) {
                    ctrl = false;
                }
            });
            $(document).on('click', '.t-row', function(e) {
                if(!ctrl) {
                    $(this).closest('.total-content').find('.sel-row').removeClass('sel-row');
                }
                $(this).addClass('sel-row');
                let obj = this;
                donor_folder = total_c.get_path($(this).closest('.total-sides').attr('data-side'));
                e.stopPropagation();
            });
            $(document).on('click', '.total-content', function(e) {
                $(this).find('.sel-row').removeClass('sel-row');
            });
            $(document).on('dragstart', '.t-row', function(e) {
                let side = $(this).closest('.total-sides').attr('data-side');
                const selectedRows = $(this).closest('.total-sides').find('.sel-row');
                const id = $(this).attr('data-id');
                buffer_obj = [];
                donor_folder = total_c.get_path(side);
                buffer_obj.push(total[side][id]);

                selectedRows.each(function() {
                    let id = $(this).attr('data-id');
                    buffer_obj.push(total[side][id]);
                });
            });
            $(document).on('dragend', '.t-row', function(e) {

            });
            $(document).on('dragover', '.total-content', function(e) {
                this.classList.add('yellow-border');
                e.preventDefault();
            });
            $(document).on('dragleave', '.total-content', function(e) {
                this.classList.remove('yellow-border');
            });
            $(document).on('drop', '.total-content', function(e) {
                let side = $(this).closest('.total-sides').attr('data-side');
                this.classList.remove('yellow-border');
                destination_folder = total_c.get_path(side);
                console.dir(buffer_obj);
                let folder_in = $(this).closest('.total-sides').find('.bread-crumbs-total').text();
                total_c.moove(buffer_obj, folder_in);
            });
            $(document).on('dblclick', '.bread-crumbs-total span', function(e) {
                let obj = this;
                let key = $(this).text();
                let side = 'left';
                let path = [];
                donor_folder = total_c.get_path(side);
                if($(obj).closest('.left-side').length === 0) {
                    side = 'right';
                    donor_folder = total_c.get_path(side);
                }
                $('.bread-crumbs-total span').each(function(e,t) {
                    let scan_folder = $(t).text();
                    if(scan_folder !== 'home') {
                        path.push(scan_folder);
                    }
                    if(scan_folder === key) {
                        return false;
                    }
                });
                path = path.join('/');
                if(path === '') {
                    path = '/';
                }
                setCookies('total_'+side, path);
                total_c.update_total_panel(path, side);
            });
            $(document).on('dblclick', '.t-row', function(e) {
                let obj = $(this);
                let side = '';
                if(obj.closest('.left-side').length > 0) {
                    side = 'left';
                } else {
                    side = 'right';
                }
                let id = obj.attr('data-id');
                let item = total[side][id];

                if(item['type'] === 'D') {
                    if(side === 'left') {
                        setCookies('total_left', item['path']);
                        total_c.update_total(item['path'], '-');
                    } else {
                        setCookies('total_right', item['path']);
                        total_c.update_total('-', item['path']);
                    }
                } else if(item['type'] === 'F') {
                    if(typeof item['SYSIMGS'] !== 'undefined') {
                        open_popup('imger', {img: item['path']});
                    } else {
                        if(typeof item['info'] !== 'undefined') {
                            let info_type = get_extention(item['name']);
                            switch(info_type) {
                                case 'image':
                                    open_popup('imger', {path: obj.find('img').attr('src')});
                                    break;
                                case 'word':
                                    open_popup('word', {path: obj.attr('data-file-name')});
                                    break;
                                case 'pdf':
                                    open_popup('pdf', {path: obj.attr('data-file-name')});
                                    break;
                                case 'txt':
                                    open_popup('txt', {path: obj.attr('data-file-name')});
                                    break;
                                case 'tabs':
                                    say('Если файл большой, необходимо подождать пока он будет структурирован...');
                                    open_popup('tabs', {path: obj.attr('data-file-name')});
                                    break;

                                default:
                                    say('Тип: '+info_type+' - не определён.');
                                    break;
                            }
                        }
                    }
                }
                console.dir(item);
            });

            function create_folder(side) {
                donor_folder = total_c.get_path(side);
                if(donor_folder === 'home/SYSIMGS') {
                    say('В этой папке запрещено создавать свои папки...', 2);
                    return false;
                }
                info_inputString(undefined, (mess) => {
                    total_c.create_folder(side, bufferText);
                }, 'Введите имя папки:', 'НОВАЯ');
            }
            function del_sellect_items(side) {
                donor_folder = total_c.get_path(side);
                info_qest(undefined, (mess)=>{
                    let del_items = [];
                    $('.total-sides.'+side+'-side .sel-row').each(function(e,t) {
                        let item = total[side][$(t).attr('data-id')];
                        del_items.push(item);
                    })
                    total_c.delete_items(del_items);
                }, (mess)=>{}, 'Подтвердите удаление выделенной<br>группы файлов (папок)', 'УДАЛИТЬ', 'Отменить удаление');
            }
            function refrash_total() {
                total_c.update();
            }
            function loading(side) {
                destination_folder = total_c.get_path(side);
                if(destination_folder === 'home/SYSIMGS') {
                    say('В эту папку запрещено загружать изображения напрямую...', 2);
                    return false;
                }
                if(isset_script('general_files') === false) {
                    include_js_script('general_files', upload_file_user(side));
                } else {
                    upload_file_user(side);
                }
            }
            function upload_file_user(side) {
                general_executer = 'loader_user_files';
                place_to_indicators_container_tag = '.total-sides.'+side+'-side';
                params_general['path'] = total_c.get_path(side);
                params_general['include'] = true;

                final_loading_ok = function() {
                    try {
                        total_c.update();
                    } catch (e) {
                        console.log('На этой странице обработчик не работает');
                    }
                };
                $('#general-input-file').click();
            }
///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
        </script>
        <?php
        echo ob_get_clean();
    }

    public static function draw()
    {
        ob_start();
        ?>
        <style>
            .total-commander {
                background-color: #e7e7e7;
                color: #000000;
                padding: 5px;
                width: 100%;
                height: 100%;
                box-sizing: border-box;
                min-height: 600px;
            }
            .total-commander * {
                box-sizing: border-box;
            }
            .total-commander .total-sides {
                background-color: rgba(255, 255, 255, 0.06);
                padding: 5px;
                flex-grow: 2;
                width: 100%;
                min-height: 100%;
                background-color: #eeeeee;
            }
            .btn-panel-total button {
                display: inline-block;
                box-sizing: border-box;
                width: 22px;
                height: 22px;
                position: relative;
            }
            .btn-panel-total button img {
                position: absolute;
                display: inline-block;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
            .btn-panel-total {
                gap: 2px;
                margin-bottom: 4px;
                padding: 1px 0;
            }
            .yellow-border {
                outline: 3px solid yellow;
            }
            .bread-crumbs-total {
                background-color: #d5d5d5;
                box-shadow: inset 1px 1px 2px #797979;
                border-radius: 4px;
                padding: 3px 4px 1px;
                font-family: Consolas, sans-serif!important;
                font-size: 13px;
                font-weight: 400;
                margin-bottom: 4px;
                min-height: 19px;
            }
            .bread-crumbs-total span:hover {
                cursor: pointer;
                background-color: rgba(255, 255, 0, 0.31);
                border-radius: 4px;
            }
            .total-sides {
                display: flex;
                flex-direction: column;
            }
            .total-content {
                background-color: #e4e4e4;
                box-shadow: inset 1px 1px 4px #b0b0b0;
                border-radius: 4px;
                font-family: Consolas, sans-serif !important;
                font-size: 14px;
                font-weight: 400;
                flex-grow: 2;
                overflow-y: auto;
                padding: 5px;
                max-height: calc(70vh - 5px);
            }
            .total-content div.flex {
                align-items: center;
            }
            .t-row {
                cursor: pointer;
                padding: 0 4px;
            }
            .t-row span {
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
            }
            .t-row:hover {
                background-color: #eeeeee;
            }
            .t-row.sel-row {
                background-color: #ffeed6;
            }
            .t-row:active {
                transform: scaleY(0.9);
            }
            .mg-5 {
                margin-bottom: 5px!important;
            }

            @media screen and (max-width: 1000px) {
                .total-sides.right-side {
                    display: none;
                }
            }
        </style>
        <section class="total-commander flex gap-10">
            <div class="total-sides left-side" data-side="left">
                <div class="total-sides-panel"><?php self::draw_tools('left'); ?></div>
                <div class="total-content"></div>
            </div>
            <div class="total-sides right-side" data-side="right">
                <div class="total-sides-panel"><?php self::draw_tools('right'); ?></div>
                <div class="total-content"></div>
            </div>
        </section>
        <?php
        echo ob_get_clean();
        self::include_js_class_for_totalCommander();
    }

    /**
     * @param $side - left or right
     * @return void
     */
    private static function draw_tools($side) {
        ob_start(); ?>
        <div class="flex bread-crumbs-total"></div>
        <div class="flex btn-panel-total">
            <button onclick="refrash_total()" class="action-btn" title="Обновить"><img src="./DOWNLOAD/20231204-130112_id-2-713791.svg" width="18" height="18"></button>
            <button onclick="" class="action-btn" title=""><img src="./DOWNLOAD/20230516-125207_id-2-411008.svg" width="18" height="18"></button>
            <button onclick="del_sellect_items('<?=$side?>')" class="action-btn" title="Удалить один или несколько выделенных объектов"><img src="./DOWNLOAD/20230907-213237_id-2-475996.svg" width="18" height="18"></button>
            <button onclick="loading('<?=$side?>')" class="action-btn" title=""><img src="./DOWNLOAD/20231103-082136_id-2-879966.svg" width="18" height="18"></button>
            <button onclick="create_folder('<?=$side?>')" class="action-btn" title="Создать новую папку"><img src="./DOWNLOAD/55a6ce68a76d9efd78145205520ff5d7.svg" width="18" height="18"></button>
        </div>
        <?php echo ob_get_clean();
    }
    private static function delete_dir($dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                self::delete_dir($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * Перемещает вайл или папку $source - полный путь в папку $destination - полный путь
     * @param $source
     * @param $destination
     * @return void
     */
    private static function move_item($source, $destination): void
    {
        if (!file_exists($source)) {
            return;
        }
        if (is_dir($source)) {
            $destinationPath = rtrim($destination, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($source);
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $iterator = new \FilesystemIterator($source, \FilesystemIterator::SKIP_DOTS);
            foreach ($iterator as $item) {
                $sourcePath = $item->getPathname();
                $newDestinationPath = $destinationPath . DIRECTORY_SEPARATOR . $item->getFilename();
                self::move_item($sourcePath, $newDestinationPath);
            }
            rmdir($source);
        } else {
            $destinationPath = is_dir($destination)
                ? rtrim($destination, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($source)
                : $destination;
            rename($source, $destinationPath);
        }
    }

}