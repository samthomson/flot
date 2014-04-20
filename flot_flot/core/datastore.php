<?php
	# handles everything to do with the data store, loading writing etc
	
	

	class DataStore {

		public $urls;
		public $items;
		public $settings;
		public $users;
		public $oncologies;

		public $s_base_path;

		function __construct() {
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			$this->initiate_settings();
			$this->initiate_urls();
			$this->initiate_items();
			$this->initiate_users();
			$this->initiate_oncologies();
		}

		function initiate_oncologies() {
			require($this->s_base_path.'flot_flot/datastore/oncologies.php');
			$this->oncologies = json_decode($oncologies);
		}
		function initiate_settings() {
			require($this->s_base_path.'flot_flot/datastore/settings.php');
			$this->settings = json_decode($settings);
		}
		function initiate_urls() {
			require($this->s_base_path.'flot_flot/datastore/urls.php');
			$this->urls = json_decode($urls);
		}
		function initiate_items() {
			require($this->s_base_path.'flot_flot/datastore/items.php');
			$this->items = json_decode($items);
		}
		function initiate_users() {
			require($this->s_base_path.'flot_flot/datastore/users.php');
			$this->users = json_decode($users);
		}
		function get_current_url_data()
		{
			foreach ($this->urls as $url) {
				#echo "compare '$url->url' with current '$_SERVER[REQUEST_URI]'";
				if ($url->url == $_SERVER['REQUEST_URI'])
					return $url;
			}
			return false;
		}
		function get_oncology($s_oncology_name)
		{
			foreach ($this->oncologies as $oncology) {
				#echo "compare '$url->url' with current '$_SERVER[REQUEST_URI]'";
				if ($oncology->id == $s_oncology_name)
					return $oncology;
			}
			return false;
		}
		function get_item_data($item_id)
		{
			foreach ($this->items as $item) {
				if ($item->id === $item_id)
					return $item;
			}
			return false;
		}
		function o_get_user_data($user_id)
		{
			foreach ($this->users as $user) {
				if ($user->user == $user_id)
					return $user;
			}
			return false;
		}
	}
?>