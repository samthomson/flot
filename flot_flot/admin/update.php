<?php
	// include core
	$s_b_p = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
	require_once($s_b_p.'flot_flot/core/base.php');;
	require_once(S_BASE_PATH.'flot_flot/core/flot.php');

	$flot = new Flot;

	// if authorized
	if(!$flot->b_is_user_admin()){
		# forward them to login page
		$flot->_page_change("/flot_flot/admin/login.php");
	}else{

		$sfSF = new SettingsUtilities;
		$fuFU = new FileUtilities;


		$s_start_version = $sfSF->s_literal_flot_version();

		// run update_before
		//$fuFU->_run_if_exists_then_delete(S_BASE_PATH.'update_before.php');

		

		//
		// do update
		//

		// download new flot
		$s_download_to = S_BASE_PATH.'/flot_flot/temp/new_flot.zip';
		echo "download to: ".$s_download_to."<br/>";
		echo "download from: ".FLOT_DOWNLOAD_URL."<br/>";
		file_put_contents($s_download_to, fopen(FLOT_DOWNLOAD_URL, 'r'));

		// unpack

		// copy each file in it to corresponding location






		// run update_after
		//$fuFU->_run_if_exists_then_delete(S_BASE_PATH.'update_after.php');
		// delete new start page
		//$flot->_delete_start_page();

		$s_end_version = $sfSF->s_literal_flot_version();

		// output
		if($s_start_version !== $s_end_version){
			echo "flot was updated from version <strong>$s_start_version</strong> to <strong>$s_end_version</strong>";
		}else{
			echo "flot didn't update, flot is version <strong>$s_end_version</strong>";
		}

	}
?>