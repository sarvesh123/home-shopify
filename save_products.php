<?php
	
	require __DIR__.'/conf.php';
	require __DIR__.'/includes/functions.php';
	
	require __DIR__.'/spreadsheet-reader/php-excel-reader/excel_reader2.php';
	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';

	$fileName = getLatestFile(PRODUCTS_FILE_PATH);

	if ( fileUnread($fileName) ) {

		markFileRead($fileName, PRODUCTS_FILE_PATH);

		ini_set('memory_limit', -1);

		$ProductsReader = new SpreadsheetReader(PRODUCTS_FILE_PATH . '/' . $fileName);

		foreach ($ProductsReader as $key => $field) {
			$arr = array();
			$arr['title'] = mysql_real_escape_string($field[0]);
			$arr['description'] = mysql_real_escape_string($field[1]);
			$arr['vendor'] = mysql_real_escape_string($field[2]);
			$arr['type'] = mysql_real_escape_string($field[3]);
			$arr['tags'] = mysql_real_escape_string($field[4]);    
			$arr['image'] = $field[21];  
			$arr['meta_description'] = mysql_real_escape_string($field[28]); 
			$arr['compare_price'] = $field[20]; 
			$arr['weight'] = $field[12]; 
			$arr['quantity'] = sumQuantity($field); 
			$arr['sku'] = $field[6]; 

			$fields = implode(',', array_keys($arr));
			$values = implode("','", array_values($arr));

			$sql = "INSERT INTO products (". $fields . ") VALUES ('" . $values . "')";
			$result = mysql_query($sql, $conn);
			if(!$result) {
				die('Could not enter data: ' . mysql_error());
			}
			echo "Entered data successfully\n";
		}
	}
	mysql_close($conn);
