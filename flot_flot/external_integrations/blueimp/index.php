<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';

require_once($s_base_path.'/flot_flot/core/flot.php');

$flot = new Flot;

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');


$a_options = array('upload_dir' => $s_base_path.$flot->datastore->settings->upload_dir);
//print_r($a_options);
$upload_handler = new UploadHandler($a_options);
