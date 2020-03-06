<h2 class="bottom_bar">Version Management</h2>
<?
	$release_log_url = 'https://www.litespeedtech.com/products/litespeed-web-server/release-log';
	echo '<p class="xtbl_value"><a target=_new href="' . $release_log_url . '">Release Notes</a></p>';

	if ($error != NULL) {
		echo '<p class="gui_error">Error: ' . $error . '</p>';
	}

	$product->getNewVersion();
	$product->getInstalled();

	if (isset($service->license['type']))
	{
?>
		<table width="100%" class="xtbl" border="0" cellpadding="5" cellspacing="1">
			<tr class="xtbl_header">
				<td colspan="5" class="xtbl_title" nowrap>License Info</td>
			</tr>
			<tr class="xtbl_label"><td width="120">Type</td><td>Serial</td><td>Expiration Date</td><td>Software Update Expiration</td><td>Action</td></tr>
			<tr class="xtbl_value"><td>
<?

	$updexpdate = $service->license['updateExpires_date'];
	$timeleft = $service->license['updateExpires'] - time();
	if ($timeleft < 172800) // 2 days
	{
		$exptag = ($timeleft < 0) ? 'Expired' : 'Expiring soon';
		$updexpdate .= '<span style="background: rgb(230, 3, 3); margin-left:6px; padding:2px 6px;border-radius: 3px;color: white;">' . $exptag . '</span>';
	}

	echo $service->license['type'] . '</td><td>'
			. $service->license['serial'] . '</td><td>'
			. $service->license['expires_date'] . '</td><td>'
			. $updexpdate . '</td><td>'
			. '<a href="javascript:vermgr_checklic()" title="Check License Status Now">Validate License</a>';
?>
	</td></tr>
	</table>

<?
	}

	if ( $product->new_release != NULL)
	{
?>
		<table width="100%" class="xtbl" border="0" cellpadding="5" cellspacing="1">
			<tr class="xtbl_header">
				<td colspan="2" class="xtbl_title" nowrap>Latest Release</td>
			</tr>
			<tr class="xtbl_label"><td width="120">Release</td><td>Action</td></tr>
			<tr class="xtbl_value"><td>
<?
		echo $product->new_version . '</td><td>';
		if ($product->isInstalled($product->new_version))
			echo '<a href="javascript:vermgr(\'download\',\'' . $product->new_version . '\')">Force Reinstall</a>';
		else
			echo '<a href="javascript:vermgr(\'download\',\'' . $product->new_version . '\')">Download/Upgrade</a>';

?>

	</td></tr>
	</table>


<?
	}
?>


		<table width="100%" class="xtbl" border="0" cellpadding="5" cellspacing="1">
			<tr class="xtbl_header">
				<td colspan="2" class="xtbl_title" nowrap>Installed Versions</td>
			</tr>
			<tr class="xtbl_label">
				<td width="120">Version</td><td>Actions</td>
			</tr>
<?
	natsort($product->installed_releases);

 	$product->installed_releases = array_reverse($product->installed_releases);


	foreach( $product->installed_releases as $rel )
	{
		echo '<tr class="xtbl_value"><td>' . $rel . '</td><td nowrap>';
		if ( $product->version !== $rel )
		{
			echo '<a href="javascript:vermgr(\'switchTo\',\'' . $rel . '\')">SwitchTo</a>&nbsp;&nbsp;';
			echo '<a href="javascript:vermgr(\'remove\',\'' . $rel . '\')">Remove</a>&nbsp;&nbsp;';
		}
		else {
			echo 'Active &nbsp;&nbsp;';
		}
		echo '<a href="javascript:vermgr(\'download\',\'' . $rel . '\')">Force Reinstall</a>';
		echo "</td></tr>\n";
	}
?>
        </table>

