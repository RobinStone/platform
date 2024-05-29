<?phpclass Upload {    public static $error = array();    static function image($FILES, $resize=false, $width=100, $height=100, $newNamePath='none') {        $exp = array('png', 'jpeg', 'jpg', 'gif', 'webp');        if($FILES['file']['error'] != 0) {            self::$error[] = 'Ошибка загрузки файла.';        } elseif ($FILES['file']['size'] < 200 || $FILES['file']['size'] > 31457280) {            self::$error[] = 'Не подходит размер.';        } else {            $temp = getimagesize($FILES['file']['tmp_name']);            preg_match('#\/([a-zA-Z]{3,4})$#ui', $temp['mime'], $matches);            //   wtf($matches);            if(isset($matches[1]) && in_array(mb_strtolower($matches[1]), $exp)) {                $id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : rand(100000, 999999);  // тянем id если есть                $name = date('Ymd-His') . '_id-' . $id . '-' . rand(100000, 999999) . '.' . mb_strtolower($matches[1]);                if(move_uploaded_file($FILES['file']['tmp_name'], '.'.Core::$DOWNLOAD.$name)) {                    $temp['systemName'] = $name;                    $temp['userName'] = $FILES['file']['name'];                    if($resize) {                        $newNamePath = ($newNamePath == 'none') ? '.'.Core::$DOWNLOAD.$name : $newNamePath;                        if($ans = Upload::resize('.'.Core::$DOWNLOAD.$name, $newNamePath, $width, $height)) {                            $temp['resizing'] = 'ok';                            $temp['newWidth'] = $ans['width'];                            $temp['newHeight'] = $ans['height'];                            if($newNamePath != '.'.Core::$DOWNLOAD.$name) {                                unlink('./download/'.$name);                            }                        }                    } else {                        list($w, $h) = getimagesize('./download/'.$name);                        $temp['newWidth'] = $w;                        $temp['newHeight'] = $h;                    }                } else {                    self::$error[] = 'Что-то пошло не так. Файл не загружен.';                }            } else {                self::$error[] = 'представленный файл не является избражением.';            }        }        if(!isset(self::$error) || empty(self::$error)) {            return $temp;        } else {            return false;        }    }    static function resize($path, $newPath, $width, $height) {        if(!file_exists($path)) {            $error[] = 'По указанному пути не оказалось целевого файла.';            return false;        } elseif(!preg_match_all("#\.([a-zA-Z]+)$#ui", $path, $bag)) {            $error[] = 'Расширение целевого файла имеет неизвесную структуру.';            return false;        } elseif(!isset($bag[1][0])) {            $error[] = 'Расширение целевого файла имеет неизвесную структуру.';            return false;        } else {            $exp = $bag[1][0];            $array = array('png', 'jpg', 'jpeg', 'gif', 'webp');            if(!in_array($exp, $array)) {                $error[] = 'Расширение целевого файла имеет недопустимый формат.';                return false;            } else {                list($w, $h) = getimagesize($path);   //   получаем размеры старой фотки/////////////////////////////////////////////////////////////////////////////////////////////////                if($w/$h < 1 && $width/$height >= 1) {                    $width = ($height/$h) * $w;                }                if($w/$h < 1 && $width/$height < 1) {                    $height = ($width/$w) * $h;                    $width = ($height/$h) * $w;                }                if($w/$h > 1) {                    if(($height/$h) * $w < $width) {                        $width = ($height/$h) * $w;                        $height = ($width/$w) * $h;                    } else {                        $height = ($width/$w) * $h;                        $width = ($height/$h) * $w;                    }                }                if($w/$h == 1){                    if($width > $height) {                        $width = ($height/$h) * $w;                    } elseif($height > $width) {                        $height=($width/$w) * $h;                    }                }/////////////////////////////////////////////////////////////////////////////////////////////////                $tmp = imagecreatetruecolor($width, $height);                switch ($exp) {                    case 'png':                        imagealphablending($tmp, false);                        imagesavealpha($tmp, true);                        $oldImg = imagecreatefrompng($path);                        imagecopyresampled($tmp, $oldImg, 0, 0, 0, 0, $width, $height, $w, $h);                        imagepng($tmp, $newPath);                        break;                    case 'jpeg':                    case 'jpg':                        $oldImg = imagecreatefromjpeg($path);                        imagecopyresampled($tmp, $oldImg, 0, 0, 0, 0, $width, $height, $w, $h);                        imagejpeg($tmp, $newPath);                        break;                    case 'webp':                        $oldImg = imagecreatefromwebp($path);                        imagecopyresampled($tmp, $oldImg, 0, 0, 0, 0, $width, $height, $w, $h);                        imagewebp($tmp, $newPath);                        break;                    case 'gif':                        $oldImg = imagecreatefromgif($path);                        $transparent_source_index=imagecolortransparent($oldImg);                        if($transparent_source_index!==-1){                            $transparent_color=imagecolorsforindex($oldImg, $transparent_source_index);                            $transparent_destination_index=imagecolorallocate($tmp, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);                            imagefill($tmp, 0, 0, $transparent_destination_index);                        }                        imagecopyresampled($tmp, $oldImg, 0, 0, 0, 0, $width, $height, $w, $h);                        imagegif($tmp, $newPath);                        break;                }                $tmp2['width'] = $width;                $tmp2['height'] = $height;                return $tmp2;   //   хотя можно вернуть и $tmp            }        }    }    static function crop($image) {        list($w_i, $h_i, $type) = getimagesize($image); // Получаем размеры и тип изображения (число)        $types = array("", "gif", "jpeg", "png", "webp"); // Массив с типами изображений        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа        self::$error[] = $ext;        if ($ext) {            $func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения            $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением        } else {            self::$error[] = 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый            return false;        }        $xxx = 0;        $yyy = 0;        if($w_i > $h_i) {            $xxx = round(($w_i-$h_i)/2);            $w_i = $h_i;            self::$error[] = 'width:'.$w_i.' height:'.$h_i.' posX:'.$xxx;        } else {            $yyy = 0;            $h_i = $w_i;        }        $img_o = imagecreatetruecolor($w_i, $h_i); // Создаём дескриптор для выходного изображения        imagecopy($img_o, $img_i, 0, 0, $xxx, $yyy, $w_i, $h_i); // Переносим часть изображения из исходного в выходное        $func = 'image'.$ext; // Получаем функция для сохранения результата        $arr = explode('/', $image);        $oldName = $arr[2];        $id = isset($_SESSION['user']) ? $_SESSION['user']['id'] : rand(100000, 999999);  // тянем id если есть        $newName = date('Ymd-His') . '_id-' . $id . '-' . rand(100000, 999999) . '.' . $ext;        unlink('./IMG/'.$oldName);        unlink('./IMG/img150x150/'.$oldName);        unlink('./IMG/mobile/'.$oldName);        $image = './img/'.$newName;        self::$error[] = $func($img_o, $image); // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции        self::$error[] = explode('/', $image)[2];        $ask = q("        UPDATE `images` SET         `name`     = '".$newName."'         WHERE        `name`     = '".$oldName."'        ");        if($ask) {            self::$error[] = 'Замена имени произведена успешно. ';        }        return self::$error;    }}