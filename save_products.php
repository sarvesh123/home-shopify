<?php
	
	require __DIR__.'/conf.php';
	require __DIR__.'/includes/functions.php';
	
	require __DIR__.'/spreadsheet-reader/php-excel-reader/excel_reader2.php';
	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';

	$fileData = getLatestFile(PRODUCTS_FILE_PATH);

	$fileName = $fileData['name'];

	if ( fileUnread($fileName) ) {

		ini_set('memory_limit', -1);

		truncateTable(TABLE_PRODUCTS);

		$ProductsReader = new SpreadsheetReader(PRODUCTS_FILE_PATH . '/' . $fileName);

		$recordsRead = 0;
		foreach ($ProductsReader as $key => $field) {
			if (!empty($field[1])) {
				$arr = array();
				$arr['name'] = $mysqli->real_escape_string($field[1]);
				$arr['description'] = $mysqli->real_escape_string($field[2]);
				$arr['vendor'] = $mysqli->real_escape_string($field[3]);
				$arr['type'] = $mysqli->real_escape_string($field[4]);
				$arr['tags'] = $mysqli->real_escape_string($field[5]);    
				$arr['image'] = $field[22];  
				$arr['meta_description'] = $mysqli->real_escape_string($field[29]); 
				$arr['compare_price'] = $field[21]; 
				$arr['weight'] = $field[13]; 
				$arr['quantity'] = sumQuantity($field); 
				$arr['sku'] = $field[7]; 

				$fields = implode(',', array_keys($arr));
				$values = implode("','", array_values($arr));

				$query = "INSERT INTO products (". $fields . ") VALUES ('" . $values . "')";
				$result = $mysqli->query($query);
				if(!$result) {
					die('Could not enter data: ' . mysql_error());
				}
				echo "Entered data successfully\r\n";
				$recordsRead++;
			}
		}

		saveFileParsedData($fileData, PRODUCTS_FILE_PATH, count($ProductsReader), $recordsRead);
	}
	else {
		echo 'No New Files to Parse';
	}
	$mysqli->close();
