<?php
if (isset($_GET)) extract($_GET,EXTR_OVERWRITE);
$dbhost = 'localhost';
$dbuser = 'dbuser';
$dbpwd = 'qaad1234';
$dbname = 'i4010db';
$dsn = "mysql:host=$dbhost;dbname=$dbname";
try {
    $db_conn = new PDO($dsn, $dbuser, $dbpwd);
}
catch (PDOException $e) {
    echo $e->getMessage();
    die ("錯誤: 無法連接到資料庫");
}
$db_conn->query("SET NAMES UTF8");

if (isset($action) && $action=='recover' && isset($cid)) {
    // Recover this item
    $sqlcmd = "SELECT * FROM namelist WHERE cid='$cid' AND valid='N'";
    try {
        $result = $db_conn->query($sqlcmd);
    } catch (PDOException $e) {
        die ("$sqlcmd 資料庫查詢失敗，請重試，若問題仍在，請通知管理單位。");
    }
    $rs = array();
    if ($result) $rs = $result->fetchall();
    if (count($rs) > 0) {
        $sqlcmd = "UPDATE namelist SET valid='Y' WHERE cid='$cid'";
        try {
            $result = $db_conn->query($sqlcmd);
        } catch (PDOException $e) {
            die ("$sqlcmd 資料庫查詢失敗，請重試，若問題仍在，請通知管理單位。");
        }
    }
}
if (isset($action) && $action=='delete' && isset($cid)) {
    // Invalid this item
    $sqlcmd = "SELECT * FROM namelist WHERE cid='$cid' AND valid='Y'";
    try {
        $result = $db_conn->query($sqlcmd);
    } catch (PDOException $e) {
        die ("$sqlcmd 資料庫查詢失敗，請重試，若問題仍在，請通知管理單位。");
    }
    $rs = array();
    if ($result) $rs = $result->fetchall();
    if (count($rs) > 0) {
        $sqlcmd = "UPDATE namelist SET valid='N' WHERE cid='$cid'";
        try {
            $result = $db_conn->query($sqlcmd);
        } catch (PDOException $e) {
            die ("$sqlcmd 資料庫查詢失敗，請重試，若問題仍在，請通知管理單位。");
        }
    }
}

$PageTitle = '單位人員資訊系統示範';
$sqlcmd = "SELECT * FROM namelist";
$sqlcmd2 = "SELECT * FROM groupname";

try {
    $result = $db_conn->query($sqlcmd);
} catch (PDOException $e) {
    die ("$sqlcmd 資料庫查詢失敗，請重試，若問題仍在，請通知管理單位。");
}

$Contacts = array();
if ($result) $Contacts = $result->fetchall();
// var_dump($Contacts);
// exit(); 

try {
    $result = $db_conn->query($sqlcmd2);
} catch (PDOException $e) {
    die ("$sqlcmd2 資料庫查詢失敗，請重試，若問題仍在，請通知管理單位。");
}
$Contacts2 = array();
if ($result) $Contacts2 = $result->fetchall();
?>
<html>
<head>  
<title>網頁程式範例首頁</title> 
</head>
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
<div style="text-align:center;margin-top:5px;font-size:20px;font-weight:bold;">
I4010 網頁程式設計與安全實務</div>
<div style="text-align:center;margin-top:10px;font-size:20px;">
I3A48黃堃祐單位人員名冊
</div>
<div style="text-align:center;margin-top:5px;">
<table border="1" width="90%" align="center">
<tr>
  <th width="15%">處理</th>
  <th width="15%">姓名</th>
  <th width="20%">電話</th>
  <th>地址</th>
  <th width="20%">單位</a></th>
</tr>
<?php
foreach ($Contacts AS $item ) {
  $cid = $item['cid'];
  $Name = $item['name'];
  $Phone = $item['phone'];
  $Address = $item['address'];
  $GroupID = $item['groupid'];
  $Valid = $item['valid'];
  $DspMsg = "'確定刪除項目?'";
  $PassArg = "'prog1.php?action=delete&cid=$cid'";
?>


<tr align="center">
  <td>
<?php
  if ($Valid=='N') {
?>
  <a href="prog1.php?action=recover&cid=<?php echo $cid; ?>">
    回復
    </a></td>
  <td><STRIKE><?php echo $Name ?></STRIKE></td>
<?php } else { ?>
  <a href="javascript:confirmation(<?php echo $DspMsg ?>, <?php echo $PassArg ?>)">
  作廢</a>&nbsp;
  <a href="contactmod.php?cid=<?php echo $cid; ?>">
  修改</a>
  </td>
  <td><?php echo $Name ?></td>   
<?php } ?>
  <td><?php echo $Phone ?></td>  
  <td><?php echo $Address ?></td>
  <td><?php echo $GroupID ?></td>        
</tr>
<?php
}
?>
</div>
</body>
<a href="index.php"><li> 回首頁 </li></a>
<a href="prog2.php"><li> prog2 </li></a>
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