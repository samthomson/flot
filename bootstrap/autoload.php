<?php

	$saAutoLoadDirectories = [
		'controllers'
	];


	foreach($saAutoLoadDirectories as $sDirectory){
		$sPath = '../app/'.$sDirectory."/*.php";
		//echo "$sPath";
		foreach (glob($sPath) as $filename)
		{
			include_once $filename;
		}
	}