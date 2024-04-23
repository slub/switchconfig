<?php if(!empty($sAddr)) { ?>

<?php if(getPortStatus($interfaces, "Gi0/1") != "none") { ?>
<tr><td colspan='13'><?php echo $sAddr."<b>-1</b>"; ?></td></tr>
<tr class='portnumbers'>
	<td>01</td>
	<td class='hidden'>03</td>
	<td class='hidden'>05</td>
	<td class='hidden'>07</td>
	<td class='hidden'>09</td>
	<td>11</td>
	<td></td>
	<td>13</td>
	<td class='hidden'>15</td>
	<td class='hidden'>17</td>
	<td class='hidden'>19</td>
	<td class='hidden'>21</td>
	<td>23</td>
	<td></td>
	<td>25</td>
	<td class='hidden'>27</td>
	<td class='hidden'>29</td>
	<td class='hidden'>31</td>
	<td class='hidden'>33</td>
	<td>35</td>
	<td></td>
	<td>37</td>
	<td class='hidden'>39</td>
	<td class='hidden'>41</td>
	<td class='hidden'>43</td>
	<td class='hidden'>45</td>
	<td>47</td>
</tr>
<?php } ?>

<tr>
	<?php echoPortTD($interfaces, "Gi0/1", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/3", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/5", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/7", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/9", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/11", $sAddr); ?>
	<td>&nbsp;</td>
	<?php echoPortTD($interfaces, "Gi0/13", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/15", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/17", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/19", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/21", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/23", $sAddr); ?>
	<td>&nbsp;</td>
	<?php echoPortTD($interfaces, "Gi0/25", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/27", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/29", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/31", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/33", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/35", $sAddr); ?>
	<td>&nbsp;</td>
	<?php echoPortTD($interfaces, "Gi0/37", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/39", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/41", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/43", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/45", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/47", $sAddr); ?>
</tr>
<tr>
	<?php echoPortTD($interfaces, "Gi0/2", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/4", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/6", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/8", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/10", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/12", $sAddr); ?>
	<td>&nbsp;</td>
	<?php echoPortTD($interfaces, "Gi0/14", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/16", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/18", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/20", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/22", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/24", $sAddr); ?>
	<td>&nbsp;</td>
	<?php echoPortTD($interfaces, "Gi0/26", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/28", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/30", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/32", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/34", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/36", $sAddr); ?>
	<td>&nbsp;</td>
	<?php echoPortTD($interfaces, "Gi0/38", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/40", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/42", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/44", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/46", $sAddr); ?>
	<?php echoPortTD($interfaces, "Gi0/48", $sAddr); ?>
</tr>

<?php if(getPortStatus($interfaces, "Gi0/1") != "none") { ?>
<tr class='portnumbers'>
	<td>02</td>
	<td class='hidden'>04</td>
	<td class='hidden'>06</td>
	<td class='hidden'>08</td>
	<td class='hidden'>10</td>
	<td>12</td>
	<td></td>
	<td>14</td>
	<td class='hidden'>16</td>
	<td class='hidden'>18</td>
	<td class='hidden'>20</td>
	<td class='hidden'>22</td>
	<td>24</td>
	<td></td>
	<td>26</td>
	<td class='hidden'>28</td>
	<td class='hidden'>30</td>
	<td class='hidden'>32</td>
	<td class='hidden'>34</td>
	<td>36</td>
	<td></td>
	<td>38</td>
	<td class='hidden'>40</td>
	<td class='hidden'>42</td>
	<td class='hidden'>44</td>
	<td class='hidden'>46</td>
	<td>48</td>
</tr>
<?php } ?>

<?php } ?>
