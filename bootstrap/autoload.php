<?php

	// vendor packages that add to the autoloader
	require __DIR__.'../../vendor/autoload.php';

	// load our own


	$saAutoLoadDirectories = [
		'config',
		'controllers',
		'models'
	];


	foreach($saAutoLoadDirectories as $sDirectory){
		$sPath = '../app/'.$sDirectory."/*.php";
		//echo "$sPath";
		foreach (glob($sPath) as $filename)
		{
			include_once $filename;
		}
	}