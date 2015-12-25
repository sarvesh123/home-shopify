<?php

	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/includes/conf.php';

	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';

	require __DIR__.'/includes/functions.php';

	$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);

	$ProductsReader = new SpreadsheetReader('products.xlsx');

	$products = getProductsArr($ProductsReader);

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
