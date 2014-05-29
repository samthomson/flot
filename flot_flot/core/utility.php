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
			$this->full_write_permissions();

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

			// root dir
			if(!$this->b_permissions($this->s_base_path, "0777")){
				array_push($this->sa_instructions, "flot needs full write access to the web directory.");
			}
			// flot_flot dir
			if(!$this->b_permissions($this->s_base_path.'/flot_flot', "0777")){
				array_push($this->sa_instructions, "flot needs full write access to the flot_flot directory.");
			}

			// still here, everything okay
			return true;
		}
		function b_permissions($s_dir, $s_perms){
			clearstatcache();
			return substr(sprintf('%o', fileperms($s_dir)), -4) === $s_perms;
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

			// generate tags for the file with different methods, then add what tags we have (if any) to the datastore for the file

			$sa_tags_for_file = array();
			foreach ($this->_sa_tags_from_filename() as $tag) {
				array_push($sa_tags_for_file, $tag);
			}
			

			if(count($sa_tags_for_file)){
				$o_Datastore->_add_file_tags($this->filename, $sa_tags_for_file);
			}
			$o_Datastore->_save_datastore("file_tags");
		}
		function _sa_tags_from_filename(){
			$sa_tags = array();
			// add the filename itself as a tag
			array_push($sa_tags, $this->filename);

			$s_dot_parts = explode('.', $this->filename);

			foreach ($s_dot_parts as $s_part) {
				array_push($sa_tags, $s_part);
			}			

			return $sa_tags;
		}
	}

	class FileBrowser {
		public $s_mode;
		public $s_base_path;

		function __construct($s_mode = "browse")
		{
			$this->s_mode = $s_mode;
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
		}
		function html_make_browser () {
			$s_return_html = '<input id="fileupload" type="file" name="files[]" data-url="/flot_flot/external_integrations/blueimp/index.php" multiple class="btn btn-info"><div id="upload_output"></div><div id="upload_progress_bar"><div class="bar" style="width: 50%;"></div></div><hr/><input type="text" class="form-control" id="file_browser_text_search" placeholder="search.."><hr/><div id="picture_browser_results">loading pics..<script>s_mode = "'.$this->s_mode.'";_pic_search();</script></div>';

			return $s_return_html;
		}
		function sa_themes_available(){
			// look up all template files in theme dir
			$sa_dirs = array_filter(glob($this->s_base_path.'/flot_flot/themes/*'), 'is_dir');

			foreach ($sa_dirs as $key => $s_dir) {
				$sa_dirs[$key] = substr($s_dir, strrpos($s_dir, '/')+1, strlen($s_dir));
			}
			return $sa_dirs;
		}
	}

	class FileUtilities {


		public $s_base_path;
		public $s_error_log_path;

		function __construct() {
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			$this->s_error_log_path = $this->s_base_path."flot_flot/log/php_error.log";
		}
		function b_errors () {
			// does error file have contents
			if(filesize($this->s_error_log_path) > 0)
				return true;
			
			return false;
		}
		function s_errors(){
			// return contents of error log file
			$html_errors = "";
			$sa_error_types = array('PHP Warning' => 0,'PHP Error' => 0,'PHP Notice' => 0);
			$sa_error_css_class = array('PHP Warning' => 'orange','PHP Error' => 'red','PHP Notice' => 'blue');


			$handle = fopen($this->s_error_log_path, "r");
			if ($handle) {
			    while (($line = fgets($handle)) !== false) {
			        // process the line read.
			        foreach ($sa_error_types as $key => $value) {
			        	if(strpos($line, $key) !== FALSE){
			        		$sa_error_types[$key]++;
			        		$html_errors .= '<div class="'.$sa_error_css_class[$key].'">'.$line.'</div>';
			        	}
			        }
			        
			    }
			} else {
			    // error opening the file.
			} 
			fclose($handle);

			$html_error_summary = "";
			foreach ($sa_error_types as $key => $value) {
				$html_error_summary .= "$key: $value<br/>";
	        }

			return $html_error_summary.'<hr/>'.$html_errors;
		}
		function _wipe_errors(){
			// empty error log file
			$f = @fopen($this->s_error_log_path, "r+");
			if ($f !== false) {
			    ftruncate($f, 0);
			    fclose($f);
			}
		}
	}
?>