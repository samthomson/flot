<?php
	# error handler


	class ErrorHandler {

		function __construct() {

		}

		function throw_404() {
			# add response header - 404
			header("HTTP/1.0 404 Not Found");
			echo "404";
			exit();
		}
		function throw_501() {
			echo "501";
			exit();
		}
	}

	class FlotRequirements {

		public $sa_instructions;
		public $s_base_path;

		function __construct() {
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			$this->sa_instructions = array();
		}

		function b_requirements_met(){
			#
			# do all tests
			#
			# permission are all 777
			if(!$this->full_write_permissions())
				array_push($this->sa_instructions, "flot needs full write access to the web directory");
			# check uploads dir is writable too
			if(!$this->uploads_full_write_permissions())
				array_push($this->sa_instructions, "flot needs full write access to the uploads directory");

			# return true or false
			if(count($this->sa_instructions) > 0)
				return false;
			else
				return true;
		}
		function sa_requirements_to_remedy(){
			return $this->sa_instructions;
		}

		#
		# requirements checks
		#
		function full_write_permissions(){
			if(substr(decoct(fileperms($this->s_base_path)), -4) === "0777"){
				return true;				
			}
			return false;			
		}
		function uploads_full_write_permissions(){
			$Datastore = new Datastore;
			if(substr(decoct(fileperms($this->s_base_path.$Datastore->settings->upload_dir)), -4) === "0777"){
				return true;				
			}
			return false;			
		}
	}
?>