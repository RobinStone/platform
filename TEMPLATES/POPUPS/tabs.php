<?php
    $path = $path ?? './DOWNLOAD/20240817-113912_id-2-524291.svg';
    $dir = SITE::$USER_LOCAL_DIR;

require './vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$reader = new Xlsx();
$spreadsheet = $reader->load($dir.$path);

// Получение активного листа
$worksheet = $spreadsheet->getActiveSheet();

// Преобразование данных в ассоциативный массив
$data = [];
foreach ($worksheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $rowData = [];
    foreach ($cellIterator as $cell) {
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cell->getColumn());
        $rowData[$columnIndex] = $cell->getValue();
    }
    if (!empty($rowData)) {
        $data[] = $rowData;
    }
}
?>
<style>
    .place-popap div {
        min-height: 100%;
    }
    .table-wrapper {
        max-width: calc(100vw - 20px);
        width: 1600px;
        max-height: calc(100vh - 90px);
        height: 100vh;
        overflow-y: auto;
        overflow-x: auto;
    }
    .table-popap {
        border-collapse: collapse;
    }
    .table-popap tr td {
        font-size: 13px;
        border: 1px solid rgba(52, 152, 219, 0.3);
    }
    .table-popap tr:first-child {
        background-color: #fff;
        position: sticky;
        top: 0;
        z-index: 90;
    }
    .table-popap td {
        padding: 2px 5px;
    }
    .table-popap th {
        padding: 2px 5px;
        font-size: 13px;
        font-weight: 800;
    }
</style>

<div class="table-wrapper">
    <table class="table-popap">
    <?php
    $columns = [];
    if(count($data) > 0) {
        $columns = array_shift($data);
        echo '<tr>';
        foreach($columns as $item) {
            echo '<th>'.$item.'</th>';
        }
        echo '</tr>';
    }
    foreach($data as $row_num=>$row) {
        echo '<tr>';
        foreach($row as $value) {
            echo '<td>'.$value.'</td>';
        }
        echo '</tr>';
    }
    ?>
    </table>
</div>

<script>

</script>