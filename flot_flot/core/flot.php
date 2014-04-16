<?php
	/* main class for everything flot.
	used to handle app entry point
	*/
	require_once('datastore.php');



	class Flot {
		function s_current_relative_url() {
			return $_SERVER['REQUEST_URI'];
		}
		function test() {
			$datastore = new DataStore;
			$item_id = $datastore->get_current_url_data()->id;
			// get the object representing the page requested
			$item = $datastore->get_item_data($item_id);
			// load in the template
			$template = file_get_contents('themes/first_theme/page.html');
			$sa_keys = array_keys(get_object_vars($item));

			foreach ($sa_keys as $key) {
				if($item->$key !== null)
					$template = str_replace("{{".$key."}}", $item->$key, $template);
			}
			echo $template;
		}

		function get_current_url_data()
		{
			
		}
	}
?>