<?php
// session_start();
require_once("../include/auth.php");
require_once('../include/gpsvars.php');
require_once('../include/configure.php');
require_once('../include/db_func.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$sqlcmd = "SELECT * FROM user WHERE loginid='$LoginID' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs) <= 0) die ('Unknown or invalid user!');
$UserGroupID = $rs[0]['groupid'];
$sqlcmd = "SELECT * FROM groupname WHERE valid='Y' AND (gid='$UserGroupID' "
    . "OR gid IN (SELECT groupid FROM privileges "
    . "WHERE loginid='$LoginID' AND privilege > 1 AND valid='Y'))";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)<=0) die('No group could be found!');  
$GroupNames = array();
$GroupIDs = '';
foreach ($rs as $item) {
    $ID = $item['gid'];
    $GroupNames[$ID] = $item['groupname'];
    $GroupIDs .= "','" . $ID;
}
$GroupIDs = "(" .  substr($GroupIDs,2) . "')";
if (isset($action) && $action=='recover' && isset($cid)) {
    // Recover this item
    $sqlcmd = "SELECT * FROM namelist WHERE cid='$cid' AND valid='N'";
    $rs = querydb($sqlcmd, $db_conn);
    if (count($rs) > 0) {
        $sqlcmd = "UPDATE namelist SET valid='Y' WHERE cid='$cid'";
        $result = updatedb($sqlcmd, $db_conn);
    }
}
if (isset($action) && $action=='delete' && isset($cid)) {
    // Invalid this item
    $sqlcmd = "SELECT * FROM namelist WHERE cid='$cid' AND valid='Y'";
    $rs = querydb($sqlcmd, $db_conn);
    if (count($rs) > 0) {
        $sqlcmd = "UPDATE namelist SET valid='N' WHERE cid='$cid'";
        $result = updatedb($sqlcmd, $db_conn);
    }
}
if (!isset($ItemPerPage)) $ItemPerPage = 2;
$PageTitle = '單位人員資訊系統示範';
$sqlcmd = "SELECT count(*) AS reccount FROM namelist WHERE groupid IN $GroupIDs";
$rs = querydb($sqlcmd, $db_conn);
$RecCount = $rs[0]['reccount'];
$TotalPage = (int) ceil($RecCount/$ItemPerPage);
if (!isset($Page)) {
    if (isset($_SESSION['CurPage'])) $Page = $_SESSION['CurPage'];
    else $Page = 1;
}
if ($Page > $TotalPage) $Page = $TotalPage;
$_SESSION['CurPage'] = $Page;
$StartRec = ($Page-1) * $ItemPerPage;
$sqlcmd = "SELECT * FROM namelist WHERE groupid IN $GroupIDs"
    . "LIMIT $StartRec,$ItemPerPage";
$Contacts = querydb($sqlcmd, $db_conn);
$PrevPage = $NextPage = '';
if ($TotalPage > 1) {
    if ($Page>1) $PrevPage = $Page - 1;
    if ($Page<$TotalPage) $NextPage = $Page + 1;   
}
$PrevLink = $NextLink = '';
if (!empty($PrevPage)) 
    $PrevLink = '<a href="contactlist.php?Page=' . $PrevPage . '">上一頁</a>';
if (!empty($NextPage)) 
    $NextLink = '<a href="contactlist.php?Page=' . $NextPage . '">下一頁</a>';
$sqlcmd = "SELECT * FROM groupname WHERE valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
$arrGroups = array();
if (count($rs)>0) {
    foreach ($rs as $item) {
        $gid = $item['gid'];
        $arrGroups["$gid"] = $item['groupname'];
    }
}
require_once ('../include/cssheader.php');
?>
<body>
<script Language="javascript">
<!--
function confirmation(DspMsg, PassArg) {
var name = confirm(DspMsg)
    if (name == true) {
      location=PassArg;
    }
}
-->
</script>
<div style="text-align:center;margin:0;font-size:20px;font-weight:bold;">
I4010 網頁程式設計與安全實務</div>
<table border="0" width="90%" align="center" cellspacing="0" cellpadding="2">
<tr>
  <td width="50%" align="left">
<?php if ($TotalPage > 1) { ?>
<form name="SelPage" method="POST" action="">
<?php if (!empty($PrevLink)) echo $PrevLink . '&nbsp;'; ?>
  第<select name="Page" onchange="submit();">
<?php 
for ($p=1; $p<=$TotalPage; $p++) { 
    echo '  <option value="' . $p . '"';
    if ($p == $Page) echo ' selected';
    echo ">$p</option>\n";
}
?>
  </select>頁 共<?php echo $TotalPage ?>頁
<?php if (!empty($NextLink)) echo '&nbsp;' . $NextLink; ?>
</form>
<?php } ?>
  <td>
  <td align="right" width="30%">
    <a href="contactadd.php">新增</a>&nbsp;
    <a href="logout.php">登出</a>
  </td>
</tr>
</table>
<div style="text-align:center;">
<table class="mistab" width="90%" align="center">
<tr>
  <th width="15%">處理</th>
  <th width="15%">姓名</th>
  <th width="20%">電話</th>
  <th>地址</th>
  <th width="20%">單位</a></th>
</tr>
<?php
foreach ($Contacts AS $item) {
  $cid = $item['cid'];
  $Name = $item['name'];
  $Phone = $item['phone'];
  $Address = $item['address'];
  $GroupID = $item['groupid'];
  $GroupName = 'N/A';
  if (isset($arrGroups["$GroupID"])) $GroupName = $arrGroups["$GroupID"];
  $Valid = $item['valid'];
  $DspMsg = "'確定刪除項目?'";
  $PassArg = "'contactlist.php?action=delete&cid=$cid'";
?>
<tr align="center">
  <td>
<?php
  if ($Valid=='N') {
?>
  <a href="contactlist.php?action=recover&cid=<?php echo $cid; ?>">
    回復
    </a></td>
  <td><STRIKE><?php echo $Name ?></STRIKE></td>
<?php } else { ?>
  <a href="javascript:confirmation(<?php echo $DspMsg ?>, <?php echo $PassArg ?>)">
  作廢</a>&nbsp;
  <a href="contactmod.php?cid=<?php echo $cid; ?>">
  修改</a>&nbsp;
  <a href="upload.php?cid=<?php echo $cid; ?>">
  照片</a>
  <a href="word.php?cid=<?php echo $cid; ?>">
  簡介</a>
  </td>
  <td><?php echo $Name ?></td>   
<?php } ?>
  <td><?php echo $Phone ?></td>  
  <td><?php echo $Address ?></td>
  <td><?php echo $GroupName ?></td>        
</tr>
<?php
}
?>
</table>
</div>
<?php 
$_SESSION['ProgID'] = 'contactlist.php';
?>
</body>
<script Language="javascript">
<!--
function confirmation(DspMsg, PassArg) {
var name = confirm(DspMsg)
    if (name == true) {
      location=PassArg;
    }
}
-->
</script>
</html>