<?php

require_once ('../inc/class/Sanitize.php');

if (isset($_GET['srcfile'])) {
    $filename = Sanitize::blinderGet('srcfile');
    if ($filename == '') {
        exit();
    }
} else {
    exit();
}

$dropFolder = dirname(dirname(__FILE__)) . '/tempload/';
$filename = $dropFolder . $filename;

if (file_exists($filename)) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Content-type: application/zip;\n"); //or yours?
    header("Content-Transfer-Encoding: binary");
    $len = filesize($filename);
    header("Content-Length: $len;\n");
    $outname = "downfile.zip";
    header("Content-Disposition: attachment; filename=\"$outname\";\n\n");

    readfile($filename);
}

?>