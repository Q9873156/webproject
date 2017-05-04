<?php
// 本程式因為目前只用一個參數即可取得照片，因此有安全疑慮，請思考如何改進！
// 變數及函式處理，請注意其順序
require_once("../include/configure.php");
$ID = $_GET['ID'];
if (!isset($ID)) exit;
$filetype = 'image/pjpeg';
$filename = $AttachDir . '/' . str_pad($ID, 8, '0', STR_PAD_LEFT) . '.jpg';
$fp = @fopen($filename,'r');
if ($fp) {
    $output = fread($fp, filesize($filename));
    header("Content-type: $filetype \n");
    header("Content-Disposition: filename=$filename \n");
    echo $output;
} else die ('File Not Exist!');

?>