<?php
// If the user supply a filename with certain characters, we will
// have problem to process. The first type of character is (\),('), and (")
// which will introduce problem in file system processing.
// The second type of characters is the '&' and '+' which will be
// interpleted differently in html GET method.
// In both cases, we use base64 encoding to encode the file name.
// However, since the base64_encode function will use (/) and (+) as
// the encoding character. The (/) will be interpleted as directory
// delimiter and the (+) will cause us problem in html GET method.
// To identified whether base64 encoding is used or not, we use q9x2
// as the prefix for identification.
function fnencode($filename) {
    $Need2Encode = 0;
    if ($filename <> addslashes($filename)) $Need2Encode = 1;
    if (strpos($filename,'&') !== FALSE) $Need2Encode = 1;
    if (strpos($filename,'#') !== FALSE) $Need2Encode = 1;
    if (strpos($filename,'+') !== FALSE) $Need2Encode = 1;
    if (strpos($filename,'/') !== FALSE) $Need2Encode = 1;
    if (substr($filename,0,4) == 'zyxa') $Need2Encode = 1;
    if ($Need2Encode) {
        $base64str = base64_encode($filename);
        $base64str = str_replace('/','_',$base64str);
        $base64str = str_replace('+','-',$base64str);
        // Add a prefix to the filename to identify base64 encryption
        $filename = 'zyxa' . $base64str;
    }
    return $filename;
}

function fndecode($filename) {
    if (substr($filename,0,4) == 'zyxa') {
        $org64str = substr($filename,4); // Strip the added prefix
        $org64str = str_replace('_','/',$org64str);
        $org64str = str_replace('-','+',$org64str);
        $filename = base64_decode($org64str);
    }
    return $filename;
}

function debase64($inpstr) {
    $org64str = str_replace('_','/',$inpstr);
    $org64str = str_replace('-','+',$org64str);
    return base64_decode($org64str);
}

function enbase64($inpstr) {
    $base64 = base64_encode($inpstr);
    $base64clean = str_replace('/','_',$base64);
    $base64clean = str_replace('+','-',$base64clean);
    return $base64clean;
}
?>