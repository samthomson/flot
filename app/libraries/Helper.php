<?php

	class Helper{

		public static function Redirect($sRelativePath)
		{
			// redirect user to new page
			$sUrl = self::base_url().$sRelativePath;
			header("Location: $sUrl");
			exit();
		}

		private function base_url($atRoot=TRUE, $atCore=FALSE, $parse=FALSE){
		    if (isset($_SERVER['HTTP_HOST'])) {
		        $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		        $hostname = $_SERVER['HTTP_HOST'];
		        $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

		        $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
		        $core = $core[0];

		        $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
		        $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
		        $base_url = sprintf( $tmplt, $http, $hostname, $end );
		    }
		    else $base_url = 'http://localhost/';

		    if ($parse) {
		        $base_url = parse_url($base_url);
		        if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
		    }

		    return $base_url;
		}
	}

	class UIHelper{

	}

	class Request{

		public static function get($sKey, $mDefault = null)
		{
			switch($_SERVER['REQUEST_METHOD'])
			{
				case 'POST':
					if(isset($_POST[$sKey]))
						return $_POST[$sKey];
					break;
				case 'GET':
					if(isset($_GET[$sKey]))
						return $_GET[$sKey];
					break;
			}
			return $mDefault;
		}
	}