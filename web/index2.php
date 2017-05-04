<?php
    require_once ("lunercalendar.php");
	if (!isset($_POST['Y'])) $Y = date('Y');
	else $Y = $_POST['Y'];

	if (!isset($_POST['M'])) $M = date('n');
	else $M = $_POST['M'];

	$FirstDate = 1;
	$LastDate = date('j', mktime(0,0,0,$M+1,0,$Y));
	$ShowDate = array();

	for ($i=0; $i<6; $i++)
    	for ($j=0; $j<7; $j++)
			$ShowDate[$i][$j] = '';
	$r = 0;
	for ($d=1; $d<=$LastDate; $d++) {
    	$w = date('w',mktime(0,1,0,$M,$d,$Y));
    	$ShowDate[$r][$w] = $d;
    	if ($w==6) $r++;
	}

	$Month = date('F',mktime(0,1,0,$M,1,$Y));
	$LastRow = 5;

	if (empty($ShowDate[$LastRow][0])) $LastRow = 4;
	if (empty($ShowDate[$LastRow][0])) $LastRow = 3;
?>

<html>
<body>
	<div style="text-align:center;margin-top:250px;font-size:30px;font-weight:bold;">
    I3A48 萬年曆
	
    <form action="" method="POST">
        <select name="Y" onchange="submit();">
<?php       for ($Ynum = 2006; $Ynum <= 2026; $Ynum++) {
	                echo '<option value="' . $Ynum . '"';
					if ($Y == $Ynum) echo 'selected';
					echo ">$Ynum</option>";
            }
?>
        </select>
        <select name="M" onchange="submit();">
<?php       for ($Mnum = 1; $Mnum <= 12; $Mnum++) {
	                echo '<option value="' . $Mnum . '"';
					if ($M == $Mnum) echo 'selected';
					echo ">$Mnum</option>";
            }
?>
        </select>
    </form>
	<table border="1" align="center">
		<tr align="center">
			<td width="25">Sun</td>
			<td width="25">Mon</td>
			<td width="25">Tue</td>
			<td width="25">Wed</td>
			<td width="25">Thu</td>
			<td width="25">Fri</td>
			<td width="25">Sat</td>
		</tr>

<?php
		for ($r=0; $r<=$LastRow; $r++) {
?>
			<tr align="center">
<?php
    		for($i=0; $i<7; $i++) {
				$Date = $ShowDate[$r][$i];
				$BgColor = '';
				if (!empty($Date)) {
					$LDay = '';
                    $DayOfMonth = date('Y-m-d', mktime(0,1,0,$M,$Date,$Y));
                    $LDay = GetLDay($DayOfMonth);
	    			$xDate = date('Ymd', mktime(0,1,0,$M,$Date,$Y));
	    			if ($xDate==date('Ymd')) $BgColor = ' bgcolor="#AAAAEE"';
					$Date .= '<br />' . $LDay;
				}
        		if ($i==0) $Date = '<span style="color:red">' . $Date . '</span>'; 
        		if ($i==6) $Date = '<span style="color:orange">' . $Date . '</span>'; 
?>
  				<td<?php echo $BgColor; ?>><?php echo $Date; ?></td>
<?php 		} 
?>
			</tr>
<?php 	}
 ?>
	</table>
	</div>
</body>
</html>

