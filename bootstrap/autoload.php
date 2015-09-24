<?php

	// vendor packages that add to the autoloader
	require __DIR__.'../../vendor/autoload.php';

	// load our own


	$saAutoLoadDirectories = [
		'config',
		'controllers',
		'models',
		'libraries'
	];


	foreach($saAutoLoadDirectories as $sDirectory){
		$sPath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$sDirectory.DIRECTORY_SEPARATOR."*.php";


		//echo "\n$sPath\n";
		foreach (glob($sPath) as $filename)
		{
			//echo $filename;
			include_once $filename;
		}
	}