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
if (count($rs)<0) die('No group could be found!');  
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

$PageTitle = '單位人員資訊系統示範';
$sqlcmd = "SELECT * FROM namelist WHERE groupid IN $GroupIDs";
if ($LoginID == 'admin') { 
        $sqlcmd = "SELECT * FROM namelist"; 
	}
$Contacts = querydb($sqlcmd, $db_conn);

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
<div style="text-align:center;margin:3px auto 1px auto;width:90%">
<span style="float:left;"><a href="contactadd.php">新增</a></span>
<span style="font-size:18px;">單位人員名冊</span>
<span style="float:right;"><a href="logout.php">登出</a></span>
</div>
<div style="text-align:center;">
<table class="mistab" width="90%" align="center">
<tr>
  <th width="15%">處理</th>
  <th width="15%">姓名</th>
  <th width="20%">電話</th>
  <th>地址</th>
  <th width="20%">單位</a></th>
</tr>
<span style="float:left;"><a href="useradd.php">新增</a></span>
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
</div>
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