<?php
	# files; file paths, reading writing, web url
	
	

	class CurrentURL {

		public $s_relative_url; # stored without the leading slash

		function __construct() {
			$this->s_relative_url = $_SERVER['REQUEST_URI'];
			if(strpos(this->s_relative_url, '/') == 0)
				$this->s_relative_url = substr($this->s_relative_url, 1); # remove leading slash
		}

		function initiate_urls() {
			require_once('datastore/urls.php');
			$this->urls = json_decode($urls);
		}
	}
?>