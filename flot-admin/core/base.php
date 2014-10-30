<?php
	/*
	initial setup, shared vars used by all of flot
	*/
	@define('S_BASE_EXTENSION', '/');
	@define('S_BASE_PATH', str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).S_BASE_EXTENSION);	
	@define('S_ERROR_LOG_PATH', S_BASE_PATH."flot-admin/log/php_error.log");


	ini_set("log_errors", 1);
	ini_set("error_log", S_ERROR_LOG_PATH);
	// don't display errors to screen, just write them to our log
	ini_set('display_errors', 'Off');

	if (!defined('PHP_VERSION_ID')) {
    	$version = explode('.', PHP_VERSION);

    	@define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	}

	@define('FLOT_VERSION_MAJOR', 0);
	@define('FLOT_VERSION_MINOR', 9);
	@define('FLOT_VERSION_BUILD', 14);


	@define('FLOT_DOWNLOAD_URL', 'https://github.com/samthomson/flot/archive/master.zip');

	@define('FLOT_REQUIRED_PERMISSIONS', 0644);
	@define('FLOT_REQUIRED_PERMISSIONS_DIRS', 0777);

	@define('FLOT_CACHE_PATH', S_BASE_PATH.'flot-admin/www/');
?>