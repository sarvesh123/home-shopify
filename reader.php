<?php
    // If you need to parse XLS files, include php-excel-reader
    require('spreadsheet-reader/php-excel-reader/excel_reader2.php');

    require('spreadsheet-reader/SpreadsheetReader.php');

    $Reader = new SpreadsheetReader('764051_20151209_132502_D.xlsx');
    foreach ($Reader as $Row)
    {
        print_r($Row);
    }
