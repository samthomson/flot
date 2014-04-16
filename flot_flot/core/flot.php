<?php
	/* main class for everything flot.
	used to handle app entry point
	*/
	require_once('datastore.php');
	require_once('items.php');



	class Flot {

		public $datastore;

		function __construct() {
			$this->datastore = new DataStore;
		}

		function create_item_from_url() {
			$item_id = $this->datastore->get_current_url_data()->id;
			// get the object representing the page requested
			$item = $this->datastore->get_item_data($item_id);
			
			$o_item = new Item($item);
			$o_item->render();
		}
	}
?>