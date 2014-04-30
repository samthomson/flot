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
	class UtilityFunctions {
		function get_full_url(){
			$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
	        return
	            ($https ? 'https://' : 'http://').
	            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
	            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
	            ($https && $_SERVER['SERVER_PORT'] === 443 ||
	            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
	            substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
		}
	}

	class ImageProcessor {
		public $full_file_path;
		public $filename;

		function __construct($s_base_path, $s_upload_dir, $s_file_name) {
			# build full system file path
			$this->full_file_path = $s_base_path.$s_upload_dir.'/'.$s_file_name;
			$this->filename = $s_file_name;
		}

		function process_and_tag_to_datastore(){

			# get tags for file
			# store file and tags in datastore
			$o_Datastore = new Datastore();
			$o_Datastore->_add_file($this->filename);
		}
	}

	class FileBrowser {
		public $s_mode;
		function __construct($s_mode = "browse")
		{
			$this->s_mode = $s_mode;
		}
		function html_make_browser () {
			$s_return_html = '<input id="fileupload" type="file" name="files[]" data-url="/flot_flot/external_integrations/blueimp/index.php" multiple class="btn btn-info"><div id="upload_output"></div><div id="upload_progress_bar"><div class="bar" style="width: 50%;"></div></div><hr/><div id="picture_browser_results">loading pics..<script>s_mode = "'.$this->s_mode.'";_pic_search();</script></div>';

			return $s_return_html;
		}
	}
?>