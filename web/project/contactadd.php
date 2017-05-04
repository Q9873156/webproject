<?php
// 使用者點選放棄新增按鈕
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
// echo 'I am here';
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
// echo 'I am here point 2';
$sqlcmd = "SELECT * FROM user WHERE loginid='$LoginID' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs) <= 0) die ('Unknown or invalid user!');
$UserGroupID = $rs[0]['groupid'];
if (!isset($GroupID))  $GroupID = $rs[0]['groupid'];
if (!isset($Name)) $Name = '';
if (!isset($Phone)) $Phone = '';
if (!isset($Address)) $Address = '';
if (!isset($Birth)) $Birth = '';
// 取出群組資料
$sqlcmd = "SELECT * FROM groupname WHERE valid='Y' AND (gid='$UserGroupID' "
    . "OR gid IN (SELECT gid FROM privileges "
    . "WHERE loginid='$LoginID' AND privilege > 1 AND valid='Y'))";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)<=0) die('No group could be found!');  
$GroupNames = array();
foreach ($rs as $item) {
    $ID = $item['gid'];
    $GroupNames[$ID] = $item['groupname'];
}
$GroupIDs = '';
foreach ($GroupNames as $ID => $GroupName) $GroupIDs .= "','" . $ID;
$GroupIDs = "(" . substr($GroupIDs,2) . "')";
if (isset($Confirm)) {   // 確認按鈕
    if (empty($Name)) $ErrMsg = '廠商不可為空白\n';
    if (empty($Phone)) $ErrMsg = '手機型號不可為空白\n';
	if (empty($Address)) $ErrMsg = '規格不可為空白\n';
    if (empty($GroupID) || $GroupID<>addslashes($GroupID)) $ErrMsg = '群組資料錯誤\n';
    
    if (empty($ErrMsg)) {
        // 確定此用戶可設定所選定群組的聯絡人資料
        $sqlcmd = "SELECT privilege FROM privileges "
            . "WHERE loginid='$LoginID' AND groupid='$GroupID' AND privilege>0";
        $rs = querydb($sqlcmd, $db_conn);
        // 若權限表未設定權限，則設為用戶的群組
        if (count($rs) <= 0) $GroupID = $UserGroupID;
        $sqlcmd='INSERT INTO namelist (name,phone,address,groupid,birth) VALUES ('
            . "'$Name','$Phone','$Address','$GroupID','$Birth')";
			
        $result = updatedb($sqlcmd, $db_conn);

        $sqlcmd = "SELECT count(*) AS reccount FROM namelist WHERE groupid IN $GroupIDs ";
        $rs = querydb($sqlcmd, $db_conn);
        $RecCount = $rs[0]['reccount'];
        $TotalPage = (int) ceil($RecCount/$ItemPerPage);
        $_SESSION['CurPage'] = $TotalPage; 

        header("Location: contactlist.php");
        exit();
    }
}
$PageTitle = '示範新增人員資料';
require_once("../include/cssheader.php");
?>
<body>
<div style="text-align:center;margin-top:5px;font-size:20px;font-weight:bold;">
I4010 網頁程式設計與安全實務</div>
<head>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script>
  $(function() {
    $.datepicker.regional['zh-TW']={
        monthNames:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],
        monthNamesShort:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],
        prevText:"上個月",
        nextText:"下個月",
        weekHeader:"星期",
        dayNames:["星期日","星期一","星期二","星期三","星期四","星期五","星期六"],
        dayNamesMin:["日","一","二","三","四","五","六"]
    };
    $.datepicker.setDefaults($.datepicker.regional["zh-TW"]);
    $( "#datepicker" ).datepicker({
        dateFormat:"yy-mm-dd",showMonthAfterYear:true,
        showOtherMonths: true, selectOtherMonths: 
        true,changeYear: true, changeMonth: true, yearRange:"-35:+1"
    });
  });
  </script>
</head>
<div align="center">
<form action="" method="post" name="inputform">
<b>新增資料</b>
<table border="1" width="60%" cellspacing="0" cellpadding="3" align="center">
<tr height="30">
  <th width="40%">廠商</th>
  <td><input type="text" name="Name" value="<?php echo $Name ?>" size="20"></td>
</tr>

    </select>
  </td>
</tr>
<tr height="30">
  <th width="40%">手機型號</th>
  <td><input type="text" name="Phone" value="<?php echo $Phone ?>" size="20"></td>
</tr>

<tr height="30">
  <th width="40%">規格</th>
  <td><input type="text" name="Address" value="<?php echo $Address ?>" size="50"></td>
</tr>

<tr height="30">
  <th width="40%">新增日期</th>
  <td><input type="text" name="Birth" id="datepicker" value="<?php echo $Birth ?>" size="50"></td>
</tr>

</table>
<input type="submit" name="Confirm" value="存檔送出">&nbsp;
<input type="submit" name="Abort" value="放棄新增">
</form>
</div>
<?php 
require_once ('../include/footer.php');
?>
</body>
</html>