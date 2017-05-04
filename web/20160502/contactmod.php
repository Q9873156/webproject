<?php
// 使用者點選放棄修改按鈕
if (isset($_POST['Abort']) && !empty($_POST['Abort'])) {
    header("Location: contactlist.php");
    exit();
}
// Authentication 認證
require_once("../include/auth.php");
// 變數及函式處理，請注意其順序
require_once("../include/gpsvars.php");
require_once("../include/configure.php");
require_once("../include/db_func.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
// 確認參數是否正確
if (!isset($cid)) die ("Parameter error!");
// 找出此用戶的群組
$sqlcmd = "SELECT * FROM user WHERE loginid='$LoginID' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs) <= 0) die ('Unknown or invalid user!');
$UserGroupID = $rs[0]['groupid'];

// Authorization 授權
// =====================================================
// 先取得這筆資料的群組，再檢查這個帳號是否有權限
$sqlcmd = "SELECT * FROM namelist WHERE cid='$cid'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs) <= 0) die("找不到編號為 $cid 之資料");
$GID = $rs[0]['groupid'];
if ($GID<>$UserGroupID) {   // 非本單位人員，看看是否有額外權限
    $sqlcmd = "SELECT privilege FROM privileges "
        . "WHERE loginid='$LoginID' and groupid='$GID' AND privilege>1";
    $rs = querydb($sqlcmd, $db_conn);
    if (count($rs) <= 0) die("您對編號 $cid 之資料無修改權限");
}
// =====================================================

// 處理使用者異動之資料
if (isset($Confirm)) {   // 確認按鈕
    if (!isset($Name) || empty($Name)) $ErrMsg = '姓名不可為空白\n';
    if (!isset($Phone) || empty($Phone)) $ErrMsg = '電話不可為空白\n';
    if (!isset($GroupID) || empty($GroupID) || $GroupID<>addslashes($GroupID)) 
        $ErrMsg = '群組資料錯誤\n';
    if (empty($ErrMsg)) {   // 資料經初步檢核沒問題
    // Demo for XSS
    //    $Name = xssfix($Name);
    //    $Phone = xssfix($Phone);
    // Demo for the reason to use addslashes
        if (!get_magic_quotes_gpc()) {
            $Name = addslashes($Name);
            $Phone = addslashes($Phone);
            $Address = addslashes($Address);
        }
        $sqlcmd="UPDATE namelist SET name='$Name',phone='$Phone',groupid='$GroupID', "
            . "address='$Address' WHERE cid='$cid'";
        $result = updatedb($sqlcmd, $db_conn);
        header("Location: contactlist.php");
        exit();
    }
}
if (!isset($Name)) {    
// 此處是在contactlist.php點選後進到這支程式，因此要由資料表將欲編輯的資料列調出
    $sqlcmd = "SELECT * FROM namelist WHERE cid='$cid'";
    $rs = querydb($sqlcmd, $db_conn);
    if (count($rs) <= 0) die('No data found');      // 找不到資料，正常應該不會發生
    $Name = $rs[0]['name'];
    $Phone = $rs[0]['phone'];
    $Address = $rs[0]['address'];
    $GroupID = $rs[0]['groupid'];
} else {    // 點選送出後，程式發現有錯誤
// Demo for stripslashes
    if (get_magic_quotes_gpc()) {
        $Name = stripslashes($Name);
        $Phone = stripslashes($Phone);
        $Address = stripslashes($Address);
    }
}
// 取出群組資料
$sqlcmd = "SELECT * FROM groupname WHERE valid='Y' AND (gid='$UserGroupID' "
    . "OR gid IN (SELECT groupid FROM privileges "
    . "WHERE loginid='$LoginID' AND privilege > 1 AND valid='Y'))";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)<=0) die('No group could be found!');  
$GroupNames = array();
foreach ($rs as $item) {
    $ID = $item['gid'];
    $GroupNames[$ID] = $item['groupname'];
}
$PageTitle = '示範修改人員資料';
require_once("../include/cssheader.php");
?>
<body>
<div style="text-align:center;margin-top:5px;font-size:20px;font-weight:bold;">
I4010 網頁程式設計與安全實務</div>
<div align="center">
<div align="text-align:center">
<form action="" method="post" name="inputform">
<input type="hidden" name="cid" value="<?php echo $cid ?>">
<b>修改人員資料</b>
<table border="1" width="60%" cellspacing="0" cellpadding="3" align="center">
<tr height="30">
  <th width="40%">姓名</th>
  <td><input type="text" name="Name" value="<?php echo $Name ?>" size="20"></td>
</tr>
<tr height="30">
  <th>單位</th>
  <td><select name="GroupID">
<?php
    foreach ($GroupNames as $ID => $GroupName) {
        echo '    <option value="' . $ID . '"';
        if ($ID == $GID) echo ' selected';
        echo ">$GroupName</option>\n";
    } 
?>
    </select>
  </td>
</tr>
<tr height="30">
  <th>電話</th>
  <td><input type="text" name="Phone" value="<?php echo $Phone ?>" size="20"></td>
</tr>
<tr height="30">
  <th>地址</th>
  <td><input type="text" name="Address" value="<?php echo $Address ?>" size="50"></td>
</tr>
</table>
<input type="submit" name="Confirm" value="存檔送出">&nbsp;
<input type="submit" name="Abort" value="放棄修改">
</form>
</div>
<?php 
require_once ('../include/footer.php');
?>
</body>
</html>