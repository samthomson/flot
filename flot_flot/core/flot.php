<?php
	/* main class for everything flot.
	used to handle app entry point
	*/

	class Flot {

		public $datastore;
		public $error_handler;
		public $s_base_path;

		public $s_current_user;

		function __construct() {
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			$this->load_all_dependencies();
			$this->datastore = new DataStore;
			$this->error_handler = new ErrorHandler;
		}

		function create_item_from_url() {
			
			$item = $this->datastore->get_current_url_data();
			if($item){
				// get the object representing the page requested
				$item = $this->datastore->get_item_data($item->id);
				
				$o_item = new Item($item, $this->s_base_path);
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
			foreach(glob($this->s_base_path.'/flot_flot/core/*') as $file) {
			    require_once($file);
			}
		}

		function b_is_user_admin()
		{
			session_start();
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
				    	session_start();
						$_SESSION['admin_user'] = $user;
				    }
				}
			}
		}
		function _kill_session()
		{
			session_start();
			session_destroy();
		}
		function _page_change($s_relative_page){
			$s_new_page = "Location: ";
			$s_new_page .= $s_relative_page;
			header($s_new_page);
			exit();
		}

		function oa_pages(){
			return $this->datastore->items;
		}
		function oa_menus(){
			return $this->datastore->menus;
		}

		function s_get_var($s_var, $s_default_return){
			if(isset($_GET[$s_var]))
				return $_GET[$s_var];
			return $s_default_return;
		}
		function s_post_var($s_var, $s_default_return){
			if(isset($_POST[$s_var]))
				return $_POST[$s_var];
			return $s_default_return;
		}
		function s_get_var_from_allowed($s_var_name, $sa_allowed, $s_default){
			$s_found = "";
			if(isset($_GET[$s_var_name]))
				$s_found = $_GET[$s_var_name];
			if(in_array($s_found, $sa_allowed))
				return $s_found;
			return $s_default;
		}
		function s_post_var_from_allowed($s_var_name, $sa_allowed, $s_default){
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
		function _render_all_pages(){
			foreach ($this->datastore->items as $item) {
				$item_to_render = new Item($item);
				$item_to_render->render();
				$item_to_render->update();
			}
		}
		function _delete_start_page(){
			$s_start_path = $this->s_base_path."start.php";
			unlink($s_start_path);
		}

		function _process_file_upload($s_upload_dir, $s_filename){
			$o_ImageProcessor = new ImageProcessor($this->s_base_path, $s_upload_dir, $s_filename);

			$o_ImageProcessor->process_and_tag_to_datastore();
		}		
	}
?>