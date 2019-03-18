<?php if(!empty($sAddr)) { ?>

<tr><td colspan='13'><?php echo $sAddr."<b>-1</b>"; ?></td></tr>
<tr class='portnumbers'>
	<td>01</td>
	<td>02</td>
	<td>03</td>
	<td>04</td>
	<td>05</td>
	<td>06</td>
	<td>07</td>
	<td>08</td>
	<td>&nbsp;</td>
	<td>09</td>
	<td>10</td>
</tr>
<tr>
	<?php echoPortTD($interfaces, "Gi0/1", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/2", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/3", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/4", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/5", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/6", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/7", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/8", $sAddr); ?>
	<td>&nbsp;</td>
	<?php echoPortTD($interfaces, "Gi0/9", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/10", $sAddr); ?>
</tr>

<?php } ?>
