<?php

	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';

	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';

	require __DIR__.'/includes/functions.php';

	//$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);

	$ProductsReader = new SpreadsheetReader('Book1.xlsx');

	//$products = getProductsArr($ProductsReader);

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

   	foreach ($ProductsReader as $key => $value) {
	   	$sql = "INSERT INTO log (data) VALUES ('" . mysql_real_escape_string(json_encode($value)) . "')";
		$retval = mysql_query( $sql, $conn );
		if(! $retval )
		{
		  die('Could not enter data: ' . mysql_error());
		}
		echo "Entered data successfully\n";
	}

   	mysql_close($conn);

	// foreach ($ProductsReader as $key => $value) {
	// 	$products[] = $value;
	// }

	exit;
	foreach($products as $product) {

	    $data = array('product' => $product);

	    echo "<pre>";
		try
		{
			# Making an API request can throw an exception
			$response = $shopify('POST /admin/products.json', array(), $data);

			print_r($response);
		}
		catch (shopify\ApiException $e)
		{
			# HTTP status code was >= 400 or response contained the key 'errors'
			echo $e;
			print_R($e->getRequest());
			print_R($e->getResponse());
		}
		catch (shopify\CurlException $e)
		{
			# cURL error
			echo $e;
			print_R($e->getRequest());
			print_R($e->getResponse());
		}
		echo "</pre>";
	}
