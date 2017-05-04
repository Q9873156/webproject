<?php
// To extract the GET, POST, and $PHP_SELF variables.
// For security reason, this script should be included before other
// self-defined variables. Otherwise, self-defined variables may be
// replaced through parameter passing from GET or POST method.
if (count($_POST)) {
    extract($_POST, EXTR_OVERWRITE);
} else {
    // In case of mixed GET and POST, we may have both GET and POST variables.
    // This is not we want. So $_GET is extracted only when no $_POST exists.
    if (count($_GET)) {
        extract($_GET, EXTR_OVERWRITE);
    }
}
if (count($_SESSION)) extract($_SESSION, EXTR_OVERWRITE);
// Get the IP address of client machine. If the client use proxy, the address
// is stored in server variable: HTTP_X_FORWARDED_FOR
if (isset($_SERVER['HTTP_VIA']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    $UserIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
else $UserIP = $_SERVER['REMOTE_ADDR'];
?>