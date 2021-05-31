<?php

$appname = 'RPG Transpiler';

session_start();
date_default_timezone_set('Europe/Paris');

define('DEBUG_MODE', true);

set_time_limit(600);

setlocale(LC_ALL, 'fr_FR');
setlocale(LC_COLLATE, 'fr_FR');

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', DEBUG_MODE);


function fnc_detailed_err($errno, $errstr, $errfile, $errline, $errcontext) {
    $var = array('errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 
        'errline' => $errline, 'errcontext' => $errcontext);
    // envoie systématique dans la log
    ob_start();
    var_dump($var);
    $dump = ob_get_clean();
    error_log($dump);
    die();
}

set_error_handler('fnc_detailed_err', E_ERROR | E_USER_ERROR);

require_once dirname(__FILE__).'/context/config_gregphplab.php' ;


// stockage du Path standard, peut être pratique dans certains cas
define('APP_PATH_STD', realpath(dirname(dirname(__FILE__))));


//require_once 'DBPagination.php';
require_once '../inc/class/Misc.php';
require_once '../inc/class/HtmlToolbox.php';
require_once '../inc/class/MenuApp.php';
require_once '../inc/class/Securize.php';
require_once '../inc/class/Sanitize.php';