<?php

ob_start(); // just in case

header("Cache-Control: no-store, no-cache, private"); //HTTP/1.1
header("Expires: -1"); //ie busting
header("Pragma: no-cache");


//set auto include path...get rid of all path headaches
ini_set('include_path',
$_SERVER['LS_SERVER_ROOT'] . 'admin/html/classes/:' .
$_SERVER['LS_SERVER_ROOT'] . 'admin/html/classes/ws/:' .
$_SERVER['LS_SERVER_ROOT'] . 'admin/html/includes/:.');

// **PREVENTING SESSION HIJACKING**
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);

// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
	ini_set('session.cookie_secure', 1);
}

date_default_timezone_set('America/New_York');

spl_autoload_register( function ($class) {
	include $class . '.php';
});
