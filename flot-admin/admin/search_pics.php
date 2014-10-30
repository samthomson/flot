<?php
	# search pics from datastore, render them with pagination

	include('../../flot-admin/core/base.php');
	include(S_BASE_PATH.'flot-admin/core/datastore.php');

	$s_mode = "browse";
	if(isset($_GET['mode']))
		$s_mode = $_GET['mode'];

	$s_query = "";
	if(isset($_GET['term']))
		$s_query = strtolower($_GET['term']);

	$o_Datastore = new Datastore();

	$s_return_html = "<script>var s_upload_dir = '".$o_Datastore->settings->upload_dir."';</script>";
	
	$s_upload_dir = $o_Datastore->settings->upload_dir;

	$oa_search_results = $o_Datastore->oa_search_pictures($s_query);
	
	foreach ($oa_search_results as $o_image) {
		$s_file_url = substr(S_BASE_EXTENSION, 0, -1)."/".$s_upload_dir."small/".$o_image;
		$s_file_name = $o_image;
		$s_onclick = "_lightbox('$s_file_url');";
		if($s_mode === "select"){
			$s_onclick = "select_picture('$s_file_name');";
		}
		$s_return_html .= '<img id="'.$o_image.'" onclick="'.$s_onclick.'" src="'.$s_file_url.'"/>';
	};
	if(count($oa_search_results) === 0){
		$s_return_html = "no results.. :(";
	}

	echo $s_return_html;
?>