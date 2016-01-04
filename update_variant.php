<?php

	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';
	require __DIR__.'/includes/functions.php';

	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';
	
	$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);

	echo "<pre>";

	$products = getShopifyProducts($shopify);

	$PricePlanReader = new SpreadsheetReader('price_plan.xlsx');

	$pricePlans = getPricePlans($PricePlanReader);
	$response = getVariants($products, $pricePlans);
	
	foreach ($response as $variantData) {

		$variantId = $variantData['id'];
		
		$values = getProductBySku($variantData['sku']);

		$basicVariants = getbasicVariants($values);
		$priceVariant = array('price' => $variantData['price']);

		$variant = array_merge( $priceVariant, $basicVariants );
		$data = array('variant' => $variant);
		
		updateVariants($shopify, $data, $variantId);
	}

	echo "</pre>";
