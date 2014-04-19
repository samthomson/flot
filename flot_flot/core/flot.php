<?php
	/* main class for everything flot.
	used to handle app entry point
	*/


	class Flot {

		public $datastore;
		public $error_handler;
		public $s_base_path;

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
			if(isset($_SESSION['admin_user']))
				return true;

			# didn't pass security
			return false;
		}
		function _handle_auth_attempt()
		{
			# validate credentials and then deal with user accordingly		
			if(isset($_POST['email']) && isset($_POST['password'])){
			    $user = $_POST['email'];
			    $pass = $_POST['password'];

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
		function s_admin_header(){
			$s_header = "";

			# bootstrap css
			$s_header .= '<link rel="stylesheet" href="/flot_flot/admin/css/bootstrap.min.css">';
			# bootstrap css
			$s_header .= '<link rel="stylesheet" href="/flot_flot/admin/css/admin_style.css">';

			# jquery js
			$s_header .= '<script src="/flot_flot/admin/js/jquery.min.js"></script>';
			# bootstrap js
			$s_header .= '<script src="/flot_flot/admin/js/bootstrap.min.js"></script>';

			return $s_header;
		}
	}
?>