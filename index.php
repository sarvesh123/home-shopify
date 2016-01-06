<?php 

	require __DIR__.'/conf.php';

	global $mysqli;

	$query = "SELECT records_count, download_time FROM " . TABLE_READ_FILES . " WHERE shopify_posted IS NULL";
	$result = $mysqli->query($query);

	if ($mysqli->affected_rows) {
		$unRead = $result->fetch_assoc();
	}

	if (isset($unRead)) : 
		$timestamp = strtotime($unRead['download_time']);
?>
	<h1>New Excel Sheet was Found</h1>
	<ul>
		<li>Records Count: <?php echo $unRead['records_count']; ?></li>
		<li>Time: <?php echo date('l dS \o\f F Y h:i:s A', $timestamp); ?></li>
	</ul>
	<a href="<?php echo SITE_URL; ?>/create_product.php">Upload to Shopify</a>
<?php
	else :
		echo 'No Records Found';
	endif;
?>
	<br>
	<a href="<?php echo SITE_URL; ?>/log.php">Log</a>
