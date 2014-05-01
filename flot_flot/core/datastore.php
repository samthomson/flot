<?php
	# handles everything to do with the data store, loading writing etc
	
	

	class DataStore {

		public $urls;
		public $items;
		public $settings;
		public $users;
		public $oncologies;
		public $pictures;
		public $file_tags;

		public $s_base_path;

		function __construct() {
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			$this->initiate_settings();
			$this->initiate_urls();
			$this->initiate_items();
			$this->initiate_users();
			$this->initiate_oncologies();
			$this->initiate_pictures();
			$this->initiate_file_tags();
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
		function initiate_pictures() {
			require($this->s_base_path.'flot_flot/datastore/pictures.php');
			$this->pictures = json_decode($pictures);
		}
		function initiate_file_tags() {
			require($this->s_base_path.'flot_flot/datastore/file_tags.php');
			$this->file_tags = json_decode($file_tags);
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

		function oa_search_pictures($s_query){
			if(empty($s_query)){
				// return all
				return $this->pictures;
			}else{

			}
		}

		#
		# Setting
		#
		function _set_item_data($new_item)
		{
			for($c_item = 0; $c_item < count($this->items); $c_item++) {
				if ($this->items[$c_item]->id === $new_item->id){
					$this->items[$c_item] = $new_item;
				}
			}
		}
		function s_new_item($s_oncology){
			# create a new item
			$s_new_id = uniqid($s_oncology);
			$s_item_template = '{"id":"'.$s_new_id.'", "title":"new '.$s_oncology.'", "description":"", "keywords":"","url":"","oncology":"'.$s_oncology.'", "author":"[current author?]", "published": "false", "content_html": "", "date_modified": "01/01/3000"}';
			array_push($this->items, json_decode($s_item_template));

			# save it to datastore
			$this->_save_datastore("items");

			# return its id
			return $s_new_id;
		}
		function _delete_item($s_id){
			$i_kill_index = -1;
			for($c_item = 0; $c_item < count($this->items); $c_item++) {
				if ($this->items[$c_item]->id === $s_id)
					$i_kill_index = $c_item;
			}
			if($i_kill_index > -1){
				unset($this->items[$i_kill_index]);
				$this->items = array_values($this->items);
			}

			$this->_save_datastore("items");
		}
		function _add_user($s_email, $s_pass){
			$s_user_template = '{"user":"'.$s_email.'", "pass": "'.$s_pass.'"}';
			array_push($this->users, json_decode($s_user_template));

			$this->_save_datastore("users");
		}

		function _add_file($s_filename){
			# create a new item
			$s_new_id = uniqid("pictures");
			$s_picture_template = '{"id":"'.$s_new_id.'", "filename":"'.$s_filename.'"}';
			array_push($this->pictures, json_decode($s_picture_template));

			# save it to datastore
			$this->_save_datastore("pictures");
		}
		function _add_file_tags($s_filename, $sa_tags){
			// set each tag with the filename, if there are filename already, add this one
			foreach ($sa_tags as $s_tag) {
				if(!isset($this->file_tags->$s_tag)){
					// no tag yet, make it
					$this->file_tags->$s_tag = array();
				}
				array_push($this->file_tags->$s_tag, $s_filename);
				//$this->file_tags->$s_tag = $s_filename;
			}	
		}

		
		#
		# Saving
		#
		function _save_datastore($s_datastore){
			switch ($s_datastore) {
				case 'items':
					$s_write_path = $this->s_base_path.'flot_flot/datastore/items.php';
					$s_new_content = "<?php ";
					$s_new_content .= '$items = \'';
					$s_new_content .= json_encode($this->items);
					$s_new_content .= "'; ?>";

					file_put_contents($s_write_path, $s_new_content);
					break;
				case 'users':
					$s_write_path = $this->s_base_path.'flot_flot/datastore/users.php';
					$s_new_content = "<?php ";
					$s_new_content .= '$users = \'';
					$s_new_content .= json_encode($this->users);
					$s_new_content .= "'; ?>";

					file_put_contents($s_write_path, $s_new_content);
					break;
				case 'pictures':
					$s_write_path = $this->s_base_path.'flot_flot/datastore/pictures.php';
					$s_new_content = "<?php ";
					$s_new_content .= '$pictures = \'';
					$s_new_content .= json_encode($this->pictures);
					$s_new_content .= "'; ?>";

					file_put_contents($s_write_path, $s_new_content);
					break;
				case 'file_tags':
					$s_write_path = $this->s_base_path.'flot_flot/datastore/file_tags.php';
					$s_new_content = "<?php ";
					$s_new_content .= '$file_tags = \'';
					$s_new_content .= json_encode($this->file_tags);
					$s_new_content .= "'; ?>";

					file_put_contents($s_write_path, $s_new_content);
					break;
			}
		}

	}
?>