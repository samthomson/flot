<?php
	/* main class for everything flot.
	used to handle app entry point
	*/


	class Flot {

		public $datastore;
		public $s_base_path;

		function __construct() {
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			$this->load_all_dependencies();
			$this->datastore = new DataStore;
		}

		function create_item_from_url() {
			$item_id = $this->datastore->get_current_url_data()->id;
			// get the object representing the page requested
			$item = $this->datastore->get_item_data($item_id);
			
			$o_item = new Item($item, $this->s_base_path);
			$o_item->render();
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
	}
?>