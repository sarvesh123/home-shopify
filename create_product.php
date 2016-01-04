<?php

	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';
	require __DIR__.'/includes/functions.php';

	$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);

	$products = getProducts();

	$shopifyProducts = getShopifyProducts($shopify);

	$shopifyCompareArr = createShopifyCompareArr($shopifyProducts);

   	if ($products) {

		foreach($products as $product) {

		    $data = array('product' => $product);

		    echo "<pre>";
		    if ( $productId = skuMatch($product['variants'][0]['sku'], $shopifyCompareArr) ) {
		    	if (isset($productId)) {
		    		unset($data['product']['variants']);
		    		updateShopifyProduct($shopify, $data, $productId);
		    	}
		    }
		    else {
		    	createShopifyProduct($shopify, $data);
		    }

			echo "</pre>";
		}
		updateShopifyPosted();
	}
	else {
		echo 'No Products found.';
	}
	$mysqli->close();