<?php
	require_once('core/flot.php');

	# main entry point, requests redirected here from htaccess/conf

	# get url

	# look up url in urls (from datastore)

	# serve 404 if not found

	# if found, load corresponding instance and render

	$flot = new Flot;
	$flot->test();
	echo "<hr/><br/>served by <a href=\"http://flot.io\" target=\"_blank\">flot</a><br/>";
?>