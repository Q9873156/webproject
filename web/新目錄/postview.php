<?php
// session_start();
require_once('../include/Pgpsvars.php');
require_once('../include/memconfigure.php');
require_once('../include/db_func.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$sqlcmd = "SELECT * FROM post ";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs) <= 0) die ('Unknown or invalid user!');


if (isset($action) && $action=='recover' && isset($cid)) {
    // Recover this item
    $sqlcmd = "SELECT * FROM post WHERE cid='$cid' AND valid='N'";
    $rs = querydb($sqlcmd, $db_conn);
    if (count($rs) > 0) {
        $sqlcmd = "UPDATE post SET valid='Y' WHERE cid='$cid'";
        $result = updatedb($sqlcmd, $db_conn);
    }
}
if (isset($action) && $action=='delete' && isset($cid)) {
    // Invalid this item
    $sqlcmd = "SELECT * FROM post WHERE cid='$cid' AND valid='Y'";
    $rs = querydb($sqlcmd, $db_conn);
    if (count($rs) > 0) {
        $sqlcmd = "UPDATE post SET valid='N' WHERE cid='$cid'";
        $result = updatedb($sqlcmd, $db_conn);
    }
}
if (!isset($ItemPerPage)) $ItemPerPage = 2;
$PageTitle = '單位人員資訊系統示範';
$sqlcmd = "SELECT count(*) AS reccount FROM post ";
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
$sqlcmd = "SELECT * FROM post  "
    . "LIMIT $StartRec,$ItemPerPage";
$Contacts = querydb($sqlcmd, $db_conn);
$PrevPage = $NextPage = '';
if ($TotalPage > 1) {
    if ($Page>1) $PrevPage = $Page - 1;
    if ($Page<$TotalPage) $NextPage = $Page + 1;   
}
$PrevLink = $NextLink = '';
if (!empty($PrevPage)) 
    $PrevLink = '<a href="postview.php?Page=' . $PrevPage . '">上一頁</a>';
if (!empty($NextPage)) 
    $NextLink = '<a href="postview.php?Page=' . $NextPage . '">下一頁</a>';
$sqlcmd = "SELECT * FROM post WHERE valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
$arrGroups = array();
if (count($rs)>0) {
    foreach ($rs as $item) {
        $seqno = $item['seqno'];
        $arrGroups["$seqno"] = $item['subject'];
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
<div style="text-align:center;margin:20px;font-size:20px;">
I3A48 黃堃祐
</div>
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

</tr>
</table>
<div style="text-align:center;">
<table class="mistab" width="90%" align="center">
<tr>
  
  <th width="20%">主題/內容</th>
  
  <th width="20%">發佈日期</th>
  <th width="20%">下檔日期</th>
</tr>
<?php
foreach ($Contacts AS $item) {
  $cid = $item['seqno'];
  $Contect = $item['content'];
  $Subject = $item['subject'];
  $Pubdate = $item['pubdate'];
  $Enddate = $item['enddate'];
  
  $Valid = $item['valid'];
  $DspMsg = "'確定刪除項目?'";
  $PassArg = "'contactlist.php?action=delete&cid=$cid'";
?>
<tr align="center">
  
  <td><?php echo $Subject  .'<br>'. $Contect ?></td>   
  
  <td><?php echo $Pubdate ?></td> 
  <td><?php echo $Enddate ?></td>   
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