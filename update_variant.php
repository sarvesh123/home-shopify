<?php

	session_start();

	require __DIR__.'/vendor/autoload.php';

	use phpish\shopify;

	require __DIR__.'/conf.php';

	require __DIR__.'/includes/functions.php';

	require __DIR__.'/spreadsheet-reader/php-excel-reader/excel_reader2.php';
	
	require __DIR__.'/spreadsheet-reader/SpreadsheetReader.php';

	echo "<pre>";

	$PricePlanReader = new SpreadsheetReader('price_plan.xls');

	$pricePlans = getPricePlans($PricePlanReader);

	$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);

	$products = getShopifyProducts($shopify);

	$response = getVariants($products, $pricePlans);

	$updatedVariantsCnt=0;

	foreach ($response as $variantData) {

		$variantId = $variantData['id'];

		$values = getProductBySku($variantData['sku']);

		$basicVariants = getbasicVariants($values);

		$priceVariant = array('price' => $variantData['price']);

		$variant = array_merge( $priceVariant, $basicVariants );

		$data = array('variant' => $variant);

		updateVariants($shopify, $data, $variantId);

		usleep(500000);

		$updatedVariantsCnt++;

	}

	echo "</pre>";

?>

	Updated <?php echo $updatedVariantsCnt; ?> Products Variants.<br />

	<a href="<?php echo SITE_URL; ?>/index.php">Home</a>
