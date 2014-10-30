<?php
	/* main class for everything flot.
	used to handle app entry point
	*/
	//$s_b_p = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
	require_once('base.php');

	class Flot {

		public $datastore;
		public $error_handler;

		public $s_current_user;

		function __construct() {
			$this->load_all_dependencies();
			$this->datastore = new DataStore;
			$this->error_handler = new ErrorHandler;
		}

		function create_item_from_url() {
			
			$item = $this->datastore->get_current_url_data();
			if($item){
				// get the object representing the page requested
				$item = $this->datastore->get_item_data($item->id);
				
				$o_item = new Item($item, S_BASE_PATH);
				$o_item->render();
			}else{
				$this->error_handler->throw_404();
			}
		}

		function load_all_dependencies() {
			# load all flot files
			$this->load_core_flot_dependencies();
			# get settings, and then load theme

			# load plugins too?
		}
		function load_core_flot_dependencies() {
			foreach(glob(S_BASE_PATH.'/flot-admin/core/*') as $file) {
			    require_once($file);
			}
		}
		function _safe_session_start(){
			if(PHP_VERSION_ID >= 50400){
				if (session_status() == PHP_SESSION_NONE) {
				    session_start();
				}
			}else{
				if(session_id() == '') {
				    session_start();
				}
			}
		}

		function b_is_user_admin()
		{
			$this->_safe_session_start();
			# is the user logged in to back end?
			if(isset($_SESSION['admin_user'])){
				$this->s_current_user = $_SESSION['admin_user'];
				return true;
			}

			# didn't pass security
			return false;
		}
		function _handle_auth_attempt()
		{
			# validate credentials and then deal with user accordingly		
			if(isset($_POST['email']) && isset($_POST['password'])){
			    $user = $_POST['email'];
			    $pass = sha1($_POST['password']);

			    $o_user = $this->datastore->o_get_user_data($user);

			    if($o_user){
				    if($user === $o_user->user && $pass === $o_user->pass){
				    	$this->_safe_session_start();
						$_SESSION['admin_user'] = $user;
				    }
				}
			}
		}
		function _kill_session()
		{
			$this->_safe_session_start();
			session_destroy();
		}
		function _page_change($s_relative_page){
			$s_new_page = "Location: ";
			$s_new_page .= substr(S_BASE_EXTENSION, 0, -1).$s_relative_page;
			header($s_new_page);
			exit();
		}

		function oa_pages(){
			return $this->datastore->items;
		}
		function oa_elements(){
			return $this->datastore->elements;
		}
		function oa_oncologies(){
			return $this->datastore->oncologies;
		}
		function oa_menus(){
			return $this->datastore->menus;
		}

		function _render_all_pages(){
			foreach ($this->datastore->items as $item) {
				$item_to_render = new Item($item);				
				$item_to_render->_set_full_item($this->datastore->o_get_full_item($item->id));
				$item_to_render->render();
				$item_to_render->update();
			}
		}
		function _delete_start_page(){
			$s_start_path = S_BASE_PATH."start.php";
			@unlink($s_start_path);
		}

		function _process_file_upload($s_upload_dir, $s_filename){
			$o_ImageProcessor = new ImageProcessor(S_BASE_PATH, $s_upload_dir, $s_filename);

			$o_ImageProcessor->process_and_tag_to_datastore();
		}		
		function _create_start_dirs(){
			// make the uploads and datastore dir
			@mkdir(S_BASE_PATH.'flot-admin/datastore', FLOT_REQUIRED_PERMISSIONS_DIRS);
			@mkdir(S_BASE_PATH.'flot-admin/uploads', FLOT_REQUIRED_PERMISSIONS_DIRS);
			@mkdir(S_BASE_PATH.'flot-admin/log', FLOT_REQUIRED_PERMISSIONS_DIRS);
			@mkdir(S_BASE_PATH.'flot-admin/temp', FLOT_REQUIRED_PERMISSIONS_DIRS);
			@mkdir(S_BASE_PATH.'flot-admin/www', FLOT_REQUIRED_PERMISSIONS_DIRS);
			// make the www directory flot caches the site to
			@mkdir(FLOT_CACHE_PATH, FLOT_REQUIRED_PERMISSIONS_DIRS);
		}

		//
		// events
		//
		function _theme_changed(){
			// regenerate all pages
			$this->_render_all_pages();
		}
	}
?>