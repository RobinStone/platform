<?php
class TOTALCOMANDER {
    public static function get_dir_catalog_of_user(string $path = "/"): bool|array
    {
        if(!is_dir('./APPLICATIONS/SHOPS/user_storages/'.Access::userID())) {
            mkdir('./APPLICATIONS/SHOPS/user_storages/'.Access::userID());
        }
        if($path !== '/' && $path !== "") {
            if($path[0] !== "/") {
                $path = "/".$path;
            }
        }
        if(is_dir('./APPLICATIONS/SHOPS/user_storages/'.Access::userID().$path)) {
            $directoryPath = './APPLICATIONS/SHOPS/user_storages/'.Access::userID().$path;
            $contents = [];
            if(is_dir($directoryPath)) {
                $items = scandir($directoryPath);
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }
                    $itemPath = $directoryPath . '/' . $item;
                    $itemStats = stat($itemPath);
                    $contents[] = [
                        'name' => $item,
                        'type' => is_dir($itemPath) ? 'D' : 'F',
                        'size' => $itemStats['size'],
                        'created' => date('Y-m-d H:i:s', $itemStats['ctime']),
                        'path' => $itemPath
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
            class TOTALCOMMANDER {
                update_total(left = '', right = '') {
                    BACK('total_commander', 'update_total', {left: left, right: right}, function (mess) {
                        mess_executer(mess, function (mess) {
                            console.dir(mess);
                        });
                    });
                };
            }
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
                background-color: #c4c4c4;
                color: #000000;
                padding: 5px;
                width: 100%;
                height: 100%;
                box-sizing: border-box;
            }
            .total-commander * {
                box-sizing: border-box;
            }
            .total-commander .total-sides {
                background-color: rgba(255, 255, 255, 0.06);
                padding: 5px;
                flex-grow: 2;
                width: 100%;
                height: 100%;
            }
        </style>
        <section class="total-commander flex gap-10">
            <div class="total-sides left-side">
                <div class="total-content"></div>
                <div class="total-sides-panel"></div>
            </div>
            <div class="total-sides right-side">
                <div class="total-content"></div>
                <div class="total-sides-panel"></div>
            </div>
        </section>
        <?php
        echo ob_get_clean();
    }
}