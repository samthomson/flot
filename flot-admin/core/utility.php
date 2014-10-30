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

	class OncologyData {
		public $o_Datastore;
		function __construct() {
			$this->o_Datastore = new Datastore;
			$this->o_Datastore->initiate_datastore('oncologies');
		}

		function oa_oncologies_available(){
			#
			# do all tests
			#
			# permission are all (as defined in base)
			$oa_oncologies_available = array();

			foreach ($this->o_Datastore->oncologies as $oncology) {
				$oa_oncologies_available[$oncology->id] = $oncology->title;
			}

			return $oa_oncologies_available;
		}
		function s_oncology_name_from_id($s_id){
			foreach ($this->o_Datastore->oncologies as $oncology) {
				if($oncology->id === $s_id){
					return $oncology->title;
				}
			}
			return 'unknown page type :(';
		}
	}

	class FlotRequirements {

		public $sa_instructions = array();

		function __construct() {
			$this->sa_instructions = array();
		}

		function b_requirements_met(){
			#
			# do all tests
			#
			# permission are all (as defined in base)
			$this->full_write_permissions();
			$this->mod_rewrite_enabled();

			# return true or false
			if(count($this->sa_instructions) > 0)
				return false;
			else
				return true;
		}

		function b_ongoing_requirements_met(){

			$o_Datastore = new Datastore;
			// flot pre-requisits
			$this->b_requirements_met();

			// upload dir writable
			if(!$this->b_permissions(S_BASE_PATH.$o_Datastore->settings->upload_dir, FLOT_REQUIRED_PERMISSIONS_DIRS)){
				array_push($this->sa_instructions, "flot needs full write access to the uploads directory: <strong>".S_BASE_PATH.$o_Datastore->settings->upload_dir."</strong>");
			}


			// a/the theme exists

			// flot has write permissions

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

			// flot-admin dir
			if(!$this->b_permissions(S_BASE_PATH.'/flot-admin', FLOT_REQUIRED_PERMISSIONS)){
				array_push($this->sa_instructions, "flot needs write access to the <strong>flot-admin</strong> directory.");
			}

			// still here, everything okay
			return true;
		}
		function mod_rewrite_enabled(){
			$suSU = new SettingsUtilities;
			$b_mod_rewrite = $suSU->b_mod_rewrite_enabled();

			if(!$b_mod_rewrite){
				array_push($this->sa_instructions, "mod_rewrite is not enabled.");
			}
		}
		function b_permissions($s_dir, $s_perms){
			/*
			clearstatcache();
			return substr(sprintf('%o', fileperms($s_dir)), -4) === $s_perms;
			*/
			return is_writable($s_dir);
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
		
		function s_get_var($s_var, $s_default_return){
			$s_var = $this->s_dot_space_var_name($s_var);
			if(isset($_GET[$s_var]))
				return $_GET[$s_var];
			return $s_default_return;
		}
		function s_post_var($s_var, $s_default_return){
			$s_var = $this->s_dot_space_var_name($s_var);
			if(@isset($_POST[$s_var]))
				return urldecode($_POST[$s_var]);
			return $s_default_return;
		}
		function s_post_array_var($s_var, $i_index, $s_default_return){
			$s_var = $this->s_dot_space_var_name($s_var);
			if(@isset($_POST[$s_var][$i_index]))
				return $_POST[$s_var][$i_index];
			return $s_default_return;
		}
		function s_get_var_from_allowed($s_var_name, $sa_allowed, $s_default){
			$s_found = "";
			$s_var_name = $this->s_dot_space_var_name($s_var_name);
			if(isset($_GET[$s_var_name]))
				$s_found = $_GET[$s_var_name];
			if(in_array($s_found, $sa_allowed))
				return $s_found;
			return $s_default;
		}
		function s_post_var_from_allowed($s_var_name, $sa_allowed, $s_default){
			$s_var_name = $this->s_dot_space_var_name($s_var_name);
			$s_found = "";
			if(isset($_POST[$s_var_name]))
				$s_found = $_POST[$s_var_name];
			if(in_array($s_found, $sa_allowed))
				return $s_found;
			return $s_default;
		}
		function b_post_vars(){
			if($_SERVER['REQUEST_METHOD'] === "POST")
				return true;
			return false;
		}
		function s_dot_space_var_name($s_original){
			return str_replace(' ', '_', str_replace('.', '_', $s_original));
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
			}else{
				echo "no tags<br/>";
			}
			$o_Datastore->b_save_datastore("file_tags");
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

		function __construct($s_mode = "browse")
		{
			$this->s_mode = $s_mode;
		}
		function html_make_browser () {
			$s_return_html = '<div class="alert alert-info">Upload pictures via the button, or by drag dropping them onto this page.</div><span class="btn btn-default fileinput-button"><input id="fileupload" type="file" name="files[]" data-url="'.S_BASE_EXTENSION.'flot-admin/external_integrations/blueimp/index.php" multiple class=""><i class="glyphicon glyphicon-cloud-upload"></i> Select files to upload from your computer/device</span><div id="upload_output"></div><div id="upload_progress_bar"><div class="bar" style="width: 50%;"></div></div><div id="upload_failure"></div><hr/><input type="text" class="form-control" id="file_browser_text_search" placeholder="search.."><hr/><div id="picture_browser_results">loading pics..<script>s_mode = "'.$this->s_mode.'";_pic_search();</script></div>';

			return $s_return_html;
		}
		function sa_themes_available(){
			// look up all template files in theme dir
			$sa_dirs = array_filter(glob(S_BASE_PATH.'/flot-admin/themes/*'), 'is_dir');

			foreach ($sa_dirs as $key => $s_dir) {
				$sa_dirs[$key] = substr($s_dir, strrpos($s_dir, '/')+1, strlen($s_dir));
			}
			return $sa_dirs;
		}
	}

	class FileUtilities {

		function __construct() {
			clearstatcache(true, S_ERROR_LOG_PATH);			
		}
		function b_errors () {
			// does error file have contents
			if(@filesize(S_ERROR_LOG_PATH) > 0)
				return true;			
			return false;
		}
		function s_errors(){
			// return contents of error log file
			$html_errors = "";
			$sa_error_types = array('PHP Warning' => 0,'PHP Error' => 0,'PHP Notice' => 0);
			$sa_error_css_class = array('PHP Warning' => 'orange','PHP Error' => 'red','PHP Notice' => 'blue');


			$handle = fopen(S_ERROR_LOG_PATH, "r");
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
			$f = @fopen(S_ERROR_LOG_PATH, "r+");
			if ($f !== false) {
			    ftruncate($f, 0);
			    fclose($f);
			}
		}

		function s_lowest_directory_of_path($s_path){
			// gets a system path to the url without the file name
			/* so www.site.com/abc/def/ghi.html might return
			'c:/wamp/www/site_local/abc/def/' 
			returns false if it can't make a directory path from url*/
			if(strpos($s_path, '.') > 0){
				// contains a dot, so has a file name in it like index.html
				$s_path = substr($s_path,0,strrpos($s_path, '/'));
			}else{
				/* no dot/filename, we'll presume it's a web path to folder like
				http://www.site.com/abc/def/
				*/
			}		
			if(is_dir($s_path))
				return $s_path;
			return false;	
		}

		function b_is_dir_empty($s_dir) {
			if (!is_readable($s_dir)) return NULL; 
				return (count(scandir($s_dir)) == 2);
		}

		function b_safely_write_file($s_path, $s_content){
			/* checks filepath against some blacklisted routes before 
			writing with file_put_contents */
			$sa_blacklist_paths = array('flot-admin', '.htaccess');
			$sa_blacklist_paths = array();
			foreach ($sa_blacklist_paths as $s_not_allowed) {
				if(strpos($s_path, $s_not_allowed) > -1){
					error_log("you tried to publish a page containing '$s_not_allowed' in it's url, that's not allowed :(");
					return false;
				}
			}
			return file_put_contents($s_path, $s_content);
		}
		function _run_if_exists_then_delete($s_path){
			// if the file exists, run it (include it), then delete it
			if(file_exists($s_path)){
				include_once($s_path);
				unlink($s_path);
			}
		}
		function _delete_update_files(){
			@unlink(S_BASE_PATH.'update_before.php');
			@unlink(S_BASE_PATH.'update_after.php');
		}
	}

	class UrlStuff{
		function s_format_url_from_item_url($s_item_url){
			// returns a relative url
			if(substr($s_item_url, 0,1) !== '/')
				$s_item_url = '/'.$s_item_url;
			if($s_item_url === "/index.html"){
				// homepage
				$s_item_url = '/';
			}
			return substr(S_BASE_EXTENSION, 0, -1).$s_item_url;
		}
	}

	class ItemURL {

		public $s_relative_url; # stored without the leading slash

		function __construct($o_item) {
			$this->s_relative_url = $o_item->url;
			if(substr($this->s_relative_url, 0, 1) === '/')
				$this->s_relative_url = substr($this->s_relative_url, 1); # remove leading slash
		}

		function has_dirs() {
			# has directories in its path
			if((strlen($this->s_relative_url) > 0) && $this->is_empty() || (strpos($this->s_relative_url, '/') > 0))
				return true;
			return false;
		}
		function is_empty() {
			# doesn't have a filename in path
			if(!strpos($this->s_relative_url, '.'))
				return true;
			return false;
		}
		function dir_path(){
			$i_end_index = strrpos($this->s_relative_url, '/');
			if(!$i_end_index)
				return $this->s_relative_url;
			return substr($this->s_relative_url, 0, $i_end_index);
		}

		function writing_filename(){
			# the filename to write the file as, this will be index.html if there was no file name
			if($this->is_empty())
				return 'index.html';
			else
			{
				$i_last_slash = strrpos($this->s_relative_url, '/');
				if($i_last_slash){
					// file was in a dir
					return substr($this->s_relative_url, $i_last_slash+1, strlen($this->s_relative_url));
				}else{
					// file exists by itself, no dir
					return $this->s_relative_url;
				}
			}
		}
		function writing_file_path($s_base_path) {
			$s_path = $s_base_path;
			if($this->has_dirs())
				$s_path .= $this->dir_path().'/';
			$s_path .= $this->writing_filename();
			return $s_path;
		}
	}

	class SettingsUtilities {
		public $s_relative_url;
		public $sa_unwritable_dirs;

		function __construct() {
		}

		function s_literal_flot_version() {
			return FLOT_VERSION_MAJOR.'.'.FLOT_VERSION_MINOR.'.'.FLOT_VERSION_BUILD;
		}
		function i_max_execution_time() {
			return ini_get('max_execution_time');
		}
		function i_max_input_time() {
			return ini_get('max_input_time');
		}
		function i_post_max_size() {
			return ini_get('post_max_size');
		}
		function i_upload_max_filesize() {
			return ini_get('upload_max_filesize');
		}
		function b_root_write_permission(){
			
			$directory_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(S_BASE_PATH));
			

			$this->sa_unwritable_dirs = array();

			foreach($directory_iterator as $filename => $path_object)
			{
				if(!is_writable($filename)){
					array_push($this->sa_unwritable_dirs, $filename);
				}
			}
			if(count($this->sa_unwritable_dirs) > 0){
				return false;
			}
			return true;
		}
		function b_mod_rewrite_enabled(){
			$b_mod_rewrite = true;
			if (function_exists('apache_get_modules')) {
				$sa_modules = apache_get_modules();
				$b_mod_rewrite = in_array('mod_rewrite', $sa_modules);
			} else {
				$b_mod_rewrite =  getenv('HTTP_MOD_REWRITE') ==='On' ? true : false ;
			}
			return $b_mod_rewrite;
		}

	}
	class JSInjector{
		public $dDatastore;

		function __construct() {
		}
		function _set_datastore($datastore){
			// pass in datastore to be used, to save initialising it twice
			$this->dDatastore = $datastore;
		}

		function s_start_script(){
			return '<script>';
		}
		function s_end_script(){
			return '</script>';
		}

		function s_page_urls(){
			$s_js = 'var sa_page_urls = [';

			$sa_urls = array();
			foreach ($this->dDatastore->items as $items => $item) {
				array_push($sa_urls, '"'.$item->url.'"');
			}
			$s_js .= implode(",", $sa_urls);
			return $s_js.'];';
		}
	}
?>