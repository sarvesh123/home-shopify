<?php

	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/includes/conf.php';
	require __DIR__.'/includes/functions.php';
	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';
	
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

	$PricePlanReader = new SpreadsheetReader('price_plan.xlsx');

	$pricePlans = getPricePlans($PricePlanReader);

	$response = getVariants($products, $pricePlans);
	
	foreach ($response as $variantData) {

		$variantId = $variantData['id'];
		
		$variant = array(
			'price' => $variantData['price']
		);

		$data = array('variant' => $variant);

		try
		{
			# Making an API request can throw an exception
			$response = $shopify('PUT /admin/variants/' . $variantId . '.json', array(), $data);
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
	}

	echo "</pre>";
