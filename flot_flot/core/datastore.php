<?php
	# handles everything to do with the data store, loading writing etc
	
	

	class DataStore {

		public $urls;
		public $items;
		public $settings;

		function __construct() {
			$this->initiate_settings();
			$this->initiate_urls();
			$this->initiate_items();
		}

		function initiate_settings() {
			require_once('datastore/settings.php');
			$this->settings = json_decode($settings);
		}
		function initiate_urls() {
			require_once('datastore/urls.php');
			$this->urls = json_decode($urls);
		}
		function initiate_items() {
			require_once('datastore/items.php');
			$this->items = json_decode($items);
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
		function get_item_data($item_id)
		{
			foreach ($this->items as $item) {
				if ($item->id == $item_id)
					return $item;
			}
			return false;
		}
	}
?>