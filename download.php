<?php

$filename = $_GET['filename'];
$path     = $_GET['path'];

ob_start();
$mm_type="application/octet-stream";
header("Cache-Control: public, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: " . $mm_type);
header("Content-Length: " .(string)(filesize($path)) );
header('Content-Disposition: attachment; filename="'.$filename.'"');
header("Content-Transfer-Encoding: binary\n");
ob_end_clean();
readfile($path);
?>