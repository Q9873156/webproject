<?php
$DocumentRoot = $_SERVER['DOCUMENT_ROOT'];
$ScriptName = explode('/',$_SERVER['PHP_SELF']);
$nArg = count($ScriptName);
$ProgName = $ScriptName[$nArg -1];
if (!file_exists($DocumentRoot.'/include')) {
    foreach ($ScriptName as $SubDir) {
        $DocumentRoot .= '/' . $SubDir;
        if (file_exists($DocumentRoot.'/include')) break;
    }
}
if (!file_exists($DocumentRoot.'/include')) $DocumentRoot = '..';
require_once($DocumentRoot.'/include/auth.php');
require_once($DocumentRoot.'/include/initialize.php');
$db_conn = connect2db($dbinfo['stucis']['host'], $dbinfo['stucis']['user'], 
    $dbinfo['stucis']['pwd'], "photos");
require_once($DocumentRoot.'/include/header.php');
$sqlcmd = "SELECT seqno,yearsem,setbyadm FROM photo WHERE id='$RegNo'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)<=0) die ("資料庫中找不到您所要的照片");
?>
<div id="logo">歷年照片</div>
<table width="80%" align="center" class="cistab">
<?php
for ($i=0; $i<count($rs); $i++) {
    $TRSent = 0;
    $Upload = '本人';
    if ($rs[$i]['setbyadm'] == 'Y') $Upload='管理者';
    if (($i%3) == 0) echo '<tr>';
?>
  <td align="center">學年學期：<?php echo $rs[$i]['yearsem'] ?><br />
    上傳者：<?php echo $Upload ?><br />
    <img src="getimage.php?ID=<?php echo $rs[$i]['seqno'] ?>&RegNo=<?php echo $RegNo ?>" 
    border="0" width="200" align="absmiddle">
  </td>
<?php
    if (($i%3)==2) {
        echo "</tr>\n";
        $TRSent = 1;
    }
}
if ($TRSent == 0) echo "</tr>\n";
echo "</table>";
require_once($DocumentRoot.'/include/footer.php');
?>