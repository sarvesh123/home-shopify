<?php
	
	require __DIR__.'/conf.php';
	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';
	require __DIR__.'/includes/functions.php';

   	$conn = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
   	if(! $conn )
   	{
    	die('Could not connect: ' . mysql_error());
   	}
   	mysql_select_db( 'shopify', $conn );

	$ProductsReader = new SpreadsheetReader($_GET['file']);

	foreach ($ProductsReader as $key => $field) {
		$arr = array();
		$arr['title'] = $field[2] . ' ' . $field[0];
	    $arr['body_html'] = $field[1];
	    $arr['vendor'] = $field[2];
	    $arr['product_type'] = $field[3];
	    $arr['tags'] = $field[4];    
	    $arr['images'] = $field[21];  
	    $arr['metafields_global_description_tag'] = $field[28]; 
	    $arr['compare_at_price'] = $field[20]; 
	    $arr['grams'] = $field[12]; 
	    $arr['inventory_quantity'] = $field[13] + $field[14] + $field[15] + $field[16]; 
	    $arr['sku'] = $field[6]; 

	    $fields = implode(',', array_keys($arr));
		$values = implode("','", array_values($arr));

	   	$sql = "INSERT INTO log (". $fields . ") VALUES ('" . $values . "')";
	   	echo "<pre>";print_r($sql);echo "</pre>";
		// $retval = mysql_query( $sql, $conn );
		// if(! $retval )
		// {
		//   die('Could not enter data: ' . mysql_error());
		// }
		// echo "Entered data successfully\n";
	}

	//$products = getProductsArr($ProductsReader);

	//print_r($ProductsReader);
	// foreach($products as $product) {

	// 	$fields = implode(',', array_keys($product));
	// 	$values = implode("','", array_values($product));

	//    	$sql = "INSERT INTO log (". $fields . ") VALUES ('" . $values . "')";
	//    	echo $sql;
	// 	// $retval = mysql_query( $sql, $conn );
	// 	// if(! $retval )
	// 	// {
	// 	//   die('Could not enter data: ' . mysql_error());
	// 	// }
	// 	// echo "Entered data successfully\n";
	// }
   	mysql_close($conn);
