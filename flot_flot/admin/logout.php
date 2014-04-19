<?php
# kill session and forward somewhere?

	require_once('../core/flot.php');


	$flot = new Flot;

	if($flot->b_is_user_admin()){
		# kill session
		$flot->_kill_session();
	}
	$flot->_page_change("/flot_flot/admin/login.php");
?>