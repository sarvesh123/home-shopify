<?php

	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';
	require __DIR__.'/includes/functions.php';

	$products = getProducts();

   	mysql_close($conn);

   	if ($products) {

		foreach($products as $product) {

		    $data = array('product' => $product);

		    echo "<pre>";
			try
			{
				$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);
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

		mysql_query("TRUNCATE TABLE " . TABLE_PRODUCTS);
	}
	else {
		echo 'No Products found.';
	}
