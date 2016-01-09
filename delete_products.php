<?php

	session_start();

	require __DIR__.'/vendor/autoload.php';

	use phpish\shopify;

	require __DIR__.'/conf.php';

	$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);

	echo "<pre>";

	try
	{
		# Making an API request can throw an exception
		$products = $shopify('GET /admin/products.json', array('published_status'=>'published'));
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

	foreach ($products as $key => $product) {

		try
		{
			# Making an API request can throw an exception
			$response = $shopify('DELETE /admin/products/' . $product['id'] . '.json', array());

			echo $key . '\r\n';
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
	}	
		
	echo "</pre>";
