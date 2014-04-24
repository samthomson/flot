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
//$util = new UtilityFunctions;

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');


//$a_options = array('upload_dir' => $s_base_path.$flot->datastore->settings->upload_dir, 'upload_url' => $util->get_full_url().'/'.$flot->datastore->settings->upload_dir);
$a_options = array('upload_dir' => $s_base_path.$flot->datastore->settings->upload_dir);
//print_r($a_options);
//$upload_handler = new UploadHandler($a_options);


class CustomUploadHandler extends UploadHandler {

	//public $flot;

	function __construct(){
		//$this->flot = new Flot;
	}

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
        $index = null, $content_range = null) {
    	try{
	        $file = parent::handle_file_upload(
	            $uploaded_file, $name, $size, $type, $error, $index, $content_range
	        );
	        if (empty($file->error)) {

	        	/*
	            $sql = 'INSERT INTO `'.$this->options['db_table']
	                .'` (`name`, `size`, `type`, `title`, `description`)'
	                .' VALUES (?, ?, ?, ?, ?)';
	            $query = $this->db->prepare($sql);
	            $query->bind_param(
	                'sisss',
	                $file->name,
	                $file->size,
	                $file->type,
	                $file->title,
	                $file->description
	            );
	            $query->execute();
	            $file->id = $this->db->insert_id;
	            */
	            //echo $file->name;
	            //$o_ImageProcessor = new ImageProcessor($flot->s_base_path, $flot->datastore->settings->upload_dir, $file->name);
	            //$o_ImageProcessor->process_and_tag_to_datastore();
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