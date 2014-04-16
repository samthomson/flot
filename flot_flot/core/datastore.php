<?php
	# handles everything to do with the data store, loading writing etc
	
	

	class DataStore {

		public $urls;
		public $items;

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
			$this->initiate_urls();
			foreach ($this->urls as $url) {
				if ($url->url == $_SERVER['REQUEST_URI'])
					return $url;
			}
			return false;
		}
		function get_item_data($item_id)
		{
			$this->initiate_items();
			foreach ($this->items as $item) {
				if ($item->id == $item_id)
					return $item;
			}
			return false;
		}
	}
?>