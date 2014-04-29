
<?php

	foreach ($o_Datastore->oa_search_pictures("") as $o_image) {
		$s_file_url = "/".$s_upload_dir."/thumbnail/".$o_image->filename;
		$s_onclick = "console.log('lightbox: $s_file_url');";
		if($this->s_mode === "select"){
			$s_onclick = "chooseFile('$s_file_url');";
		}
		$s_return_html .= '<img onclick="'.$s_onclick.'" src="'.$s_file_url.'"/>';
	};

?>