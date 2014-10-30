<?php
# kill session and forward somewhere?

	$s_b_p = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
	require_once($s_b_p.'flot-admin/core/base.php');
	require_once(S_BASE_PATH.'flot-admin/core/flot.php');


	$flot = new Flot;

	if($flot->b_is_user_admin()){
		# kill session
		$flot->_kill_session();
	}
	$flot->_page_change("/flot-admin/admin/login.php");
?>