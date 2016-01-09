<?php

	error_reporting(E_ALL);

	ini_set("display_errors", 1);

	ini_set('memory_limit', -1);

	define('SHOPIFY_SHOP', 'paramount-business-products.myshopify.com');

	define('SHOPIFY_APP_API_KEY', '2b047f028a75f3468b8b8c1cd288b480');

	define('SHOPIFY_APP_PASSWORD', '41665f150343db7da657bdd97891497a');

	define('IMAGE_BASE_URL', 'http://content.oppictures.com/Master_Images/Master_Variants/Variant_400/');

	define('PRICE_X4C2', 'Default Title');

	define('PRICE_X4C3', 'Default Title (X4C3)');

	define('PRICE_X6C1', 'Default Title (X6C1)');

	define('PRICE_X6C2', 'Default Title (X6C2)');

	define('PRICE_X6C3', 'Default Title (X6C3)');

	define('PRODUCTS_FILE_PATH', __DIR__.'/downloads');

	define('DB_HOST', 'localhost');

	define('DB_USERNAME', 'sjinnovation');

	define('DB_PASSWORD', 'sjzu+tr@');

	$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, 'pbp_2016');

	if (mysqli_connect_errno()) {

		printf("Connect failed: %s\n", mysqli_connect_error());

		exit();

	}

	$mysqli->set_charset("utf8");

	define('TABLE_READ_FILES', 'read_files');

	define('TABLE_PRODUCTS', 'products');

	define('SITE_URL', 'http://pb.bloom7.club');
