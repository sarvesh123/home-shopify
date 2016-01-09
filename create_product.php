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

	$createdProdCnt=0;

	$updatedProdCnt=0;

	if ($products) {

		ini_set('max_execution_time', 0);

		set_time_limit(0);

		$time1 = microtime(true);

		foreach($products as $product) {

			$data = array('product' => $product);

			echo "<pre>";

			if ( $productId = skuMatch($product['variants'][0]['sku'], $shopifyCompareArr) ) {

				if (isset($productId)) {

					unset($data['product']['variants']);

					updateShopifyProduct($shopify, $data, $productId);

					$updatedProdCnt++;

				}

			}

			else {
				createShopifyProduct($shopify, $data);

				$createdProdCnt++;

			}

			echo "</pre>";

			markProductRead($product['variants'][0]['sku']);
		}

		updateShopifyPosted();

	}

	else {

		echo 'No Products found.';

	}

	$mysqli->close();

/*
	if ($products) : 

?>

		Created <?php echo $createdProdCnt; ?> Products.<br />

		Updated <?php echo $updatedProdCnt; ?> Products.<br />

		<a href="<?php echo SITE_URL; ?>/update_variant.php">Update Variants</a>

<?php		

	endif;