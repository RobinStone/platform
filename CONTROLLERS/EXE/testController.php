<?php//$row = SQL_ONE_ROW(q("SELECT * FROM test WHERE id=13"))['json'];//$line = unserialize($row);////wtf(get_attachment_value($line, 'name.all.0.name'), 1);////wtf($line);//require './vendor/autoload.php';//use PhpOffice\PhpSpreadsheet\Spreadsheet;//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;//////wtf();////// Загрузка файла XLSX//$reader = new Xlsx();//$spreadsheet = $reader->load($_SERVER['DOCUMENT_ROOT'].'/RESURSES/111.xlsx');////// Получение активного листа//$worksheet = $spreadsheet->getActiveSheet();////// Преобразование данных в ассоциативный массив//$data = [];//foreach ($worksheet->getRowIterator() as $row) {//    $cellIterator = $row->getCellIterator();//    $cellIterator->setIterateOnlyExistingCells(false);//    $rowData = [];//    foreach ($cellIterator as $cell) {//        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cell->getColumn());//        $rowData[$columnIndex] = $cell->getValue();//    }//    if (!empty($rowData)) {//        $data[] = $rowData;//    }//}////wtf($data);//==============================================================//==============================================================//==============================================================//INCLUDE_CLASS('shops', 'pay');//$ans = PAY::buy(Access::userID(), 5, 'Списание', 'Кошелёк', 'Смена телефонного номера');////wtf($ans, 1);//$P = new PROFIL(Access::userID());//wtf($P->get_field('login'), 1);//$P->add_alert(ALERT_TYPE::WARNING, ['text'=>'Необходимо сменить имя, для того, что бы вы могли размещать объявления на этом сайте.', 'link'=>'/profil?title=account'], 'change_name');//$P->add_alert(ALERT_TYPE::ATTANTION, ['text'=>'Желательно сменить пароль на этом сайте, по скольку сейчас паролем является код активации, который вы получили по СМС', 'link'=>'/profil?title=account'], 'change_pass');//$P->add_alert(ALERT_TYPE::MESSAGE, ['text'=>'Обратите внимание на секцию ОПОВЕЩЕНИЯ, если вы продавец - она вам будет полезна', 'link'=>'/profil?title=account#alerts'], 'attantion_on_notification');//$P->add_alert(ALERT_TYPE::MESSAGE, 'sdkfbdsjf ывапы вапыва sgjdf sjdf');//$P->add_alert(ALERT_TYPE::MESSAGE, ['text'=>'редирект на корзину', 'link'=>'/basket']);//$P->delete_alert(ALERT_TYPE::MESSAGE, 1);//INDEXER::update();//SUBD::dump('mysqldump', ['users']);//$rows = SQL_ROWS_FIELD(q("SELECT * FROM users WHERE login = 'robin'", true), 'login');//wtf($rows, 1);//CACHE::clear_cache();