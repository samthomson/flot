<?php
	/*
	initial setup, shared vars used by all of flot
	*/
	$S_BASE_PATH = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
	$S_ERROR_LOG_PATH = $S_BASE_PATH."flot_flot/log/php_error.log";

	function s_error_log_path(){
		return $S_ERROR_LOG_PATH;
	}

	ini_set("log_errors", 1);
	ini_set("error_log", $S_ERROR_LOG_PATH);

	if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
?>