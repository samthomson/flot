<?php
	# base for all items, there will be flexible taxonomies, so how this page will work..

	require_once('../core/flot.php');

	$flot = new Flot;

	if(!$flot->b_is_user_admin()){
		# forward them to login page
		$flot->_page_change("/flot_flot/admin/login.php");
	}









?>