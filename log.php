<?php 

	require __DIR__.'/conf.php';

	global $mysqli;

	$query = "SELECT shopify_posted, records_count FROM " . TABLE_READ_FILES . "";
	$result = $mysqli->query($query);

	if ($mysqli->affected_rows) {
		echo '<table border="1" cellpadding="5"><tr>';
				echo '<td>Posted to Shopify</td>';
				echo '<td>Records Read</td>';
			echo '</tr>';
		while($row = $result->fetch_array()) {
			echo '<tr>';
				echo '<td>' . $row['shopify_posted'] . '</td>';
				echo '<td>' . $row['records_count'] . '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
?>
	<br>
	<a href="<?php echo SITE_URL; ?>/index.php">Home</a>
