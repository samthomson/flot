<?php
	# search pics from datastore, render them with pagination
	$s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';

	include($s_base_path.'flot_flot/core/datastore.php');

	$s_mode = "browse";
	if(isset($_GET['mode']))
		$s_mode = $_GET['mode'];

	$o_Datastore = new Datastore();

	$s_return_html = "";
	$s_upload_dir = $o_Datastore->settings->upload_dir;
	
	foreach ($o_Datastore->oa_search_pictures("") as $o_image) {
		$s_file_url = "/".$s_upload_dir."/thumbnail/".$o_image->filename;
		$s_onclick = "console.log('lightbox: $s_file_url');";
		if($s_mode === "select"){
			$s_onclick = "chooseFile('$s_file_url');";
		}
		$s_return_html .= '<img onclick="'.$s_onclick.'" src="'.$s_file_url.'"/>';
	};

	echo $s_return_html;
?>