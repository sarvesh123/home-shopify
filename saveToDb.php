<?php

	require __DIR__.'/conf.php';

	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';

	require __DIR__.'/includes/functions.php';

   	$dbhost = 'localhost';
   	$dbuser = 'root';
   	$dbpass = 'root';
   	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
   	if(! $conn )
   	{
    	die('Could not connect: ' . mysql_error());
   	}
   	echo 'Connected successfully';
   	mysql_select_db( 'shopify', $conn );

	$ProductsReader = new SpreadsheetReader('Book1.xlsx');

	$products = getProductsArr($ProductsReader);

	foreach($products as $product) {

		$fields = implode(',', array_keys($product));
		$values = implode("','", array_values($product));

	   	$sql = "INSERT INTO log (". $fields . ") VALUES ('" . $values . "')";
		$retval = mysql_query( $sql, $conn );
		if(! $retval )
		{
		  die('Could not enter data: ' . mysql_error());
		}
		echo "Entered data successfully\n";
	}
   	mysql_close($conn);
