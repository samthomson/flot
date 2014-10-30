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


require_once('../../../flot-admin/core/base.php');
require_once(S_BASE_PATH.'flot-admin/core/flot.php');

$flot = new Flot;
//$util = new UtilityFunctions;

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');


//$a_options = array('upload_dir' => $s_base_path.$flot->datastore->settings->upload_dir, 'upload_url' => $util->get_full_url().'/'.$flot->datastore->settings->upload_dir);
$oa_image_sizes = array();

$oa_image_sizes[''] = array();
$oa_image_sizes['']['auto_orient'] = true;


foreach ($flot->datastore->settings->thumb_sizes as $image_size) {
	$oa_image_sizes[$image_size->name] = array();

	if(isset($image_size->max_width)){
		$oa_image_sizes[$image_size->name]['max_width'] = $image_size->max_width;
	}
	if(isset($image_size->max_height)){
		$oa_image_sizes[$image_size->name]['max_height'] = $image_size->max_height;
	}
}

#print_r($oa_image_sizes);

$a_options = array(
	'upload_dir' => S_BASE_PATH.$flot->datastore->settings->upload_dir,
	'image_versions' => $oa_image_sizes,
	'accept_file_types' => '/\.(gif|jpe?g|png)$/i'
);
//$upload_handler = new UploadHandler($a_options);


class CustomUploadHandler extends UploadHandler {
	public $flot;

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {
    	try{
    		$this->flot = new Flot;
	        $file = parent::handle_file_upload(
	            $uploaded_file, $name, $size, $type, $error, $index, $content_range
	        );
	        if (empty($file->error)) {
	            $o_ImageProcessor = new ImageProcessor(S_BASE_PATH, S_BASE_EXTENSION.$this->flot->datastore->settings->upload_dir, $file->name);
	            $o_ImageProcessor->process_and_tag_to_datastore();
	        }else{
	        	echo $file->error;
	        }
	        return $file;
	    }catch (Exception $e){
	    	echo $e;
	    }
    }
}

$upload_handler = new CustomUploadHandler($a_options);