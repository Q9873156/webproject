<HTML>
<HEAD>
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=utf8">
<meta HTTP-EQUIV="Expires" CONTENT="Tue, 01 Jan 1980 1:00:00 GMT">
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="stylesheet" title="Default" href="../css/i4010.css" type="text/css" />
<title>登入系統</title>
</HEAD>
<script type="text/javascript">
<!--
function setFocus()
{
<?php if (empty($ID)) { ?>
    document.LoginForm.ID.focus();
<?php } else { ?>
    document.LoginForm.PWD.focus();
<?php } ?>
}
//-->
</script>
<body onload="setFocus()">
<div style="text-align:center;margin-top:20px;font-size:30px;font-weight:bold;">
I4010 網頁程式設計與安全實務
</div>
<div style="text-align:center;margin:20px;font-size:20px;">
手機介紹網站
</div>
<div style="text-align:center">
請於下方選擇登入。
<form method="POST" name="LoginForm" action="">
<table width="300" border="1" cellspacing="0" cellpadding="2"
align="center" bordercolor="Blue">
<tr bgcolor="#FFCC33" height="35">
<td align="center">登入系統</td>
</tr>
<tr bgcolor="#FFFFCC" height="35">
<td align="center"><p><a href="memcontactlist.php">一般登入</a></p></td>
</tr>
<tr bgcolor="#FFFFCC" height="35">
<td align="center"><p><a href="member.php">管理登入</a></p></td>
</table>
</form>
<?php if (!empty($ErrMsg)) echo $ErrMsg; ?>
</body>
</html>