<?php
require_once('HTMLPurifier.auto.php');
function xsspurify($InString) {
    $config = HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'UTF-8'); 
    $config->set('HTML.Doctype', 'XHTML 1.0 Transitional'); 
    $config->set('Cache.SerializerPath', '/tmp');
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($InString);
}
