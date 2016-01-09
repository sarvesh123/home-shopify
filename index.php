<?php 

	echo __DIR__;

	require __DIR__.'/conf.php';

	require __DIR__.'/includes/functions.php';

	$fileData = getLatestFile(PRODUCTS_FILE_PATH);

	$fileName = $fileData['name'];

	if ( fileUnread($fileName) ) {

		echo 'New Excel File Found. <a href="' . SITE_URL . '/save_products.php">Save New Excel File to DB</a><br />';
	
	}
	else {

		echo 'No New Excel File Found.<br />';
	
	}

	global $mysqli;

	$query = "SELECT records_count, download_time FROM " . TABLE_READ_FILES . " WHERE shopify_posted IS NULL";

	$result = $mysqli->query($query);

	if ($mysqli->affected_rows) {

		$unRead = $result->fetch_assoc();

	}

	if (isset($unRead)) : 

		$timestamp = strtotime($unRead['download_time']);

?>

	<h2>New Excel Sheet was Added to Database.</h2>

	<ul>

		<li>Records Count: <?php echo $unRead['records_count']; ?></li>

		<li>Time: <?php echo date('l dS \o\f F Y h:i:s A', $timestamp); ?></li>

	</ul>

	<a href="<?php echo SITE_URL; ?>/create_product.php">Upload to Shopify</a>

<?php

	else :

		echo 'No New Records Found';

	endif;

?>

	<br>

	<a href="<?php echo SITE_URL; ?>/log.php">Log</a>
