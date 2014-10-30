<?php
	# handles everything to do with the data store, loading writing etc
	
	class DataStore {

		public $urls;
		public $items;
		public $oa_individual_items = array();
		public $elements;
		public $oa_individual_elements = array();
		public $menus;
		public $settings;
		public $users;
		public $oncologies;
		public $pictures;
		public $file_tags;

		public $s_base_path;
		public $b_user_is_admin = false;

		function __construct() {
			$sa_datastores_to_initiate = array('settings', 'urls', 'items', 'elements', 'menus', 'users', 'oncologies', 'pictures', 'file_tags');

			foreach ($sa_datastores_to_initiate as $s_datastore_to_initiate) {
				$this->initiate_datastore($s_datastore_to_initiate);
			}
		}


		function initiate_datastore($s_datastore_name){
			// if we could read in datastore file, initiate object to it
			$s_filepath = S_BASE_PATH.'flot-admin/datastore/'.$s_datastore_name.'.php';
			clearstatcache(true, $s_filepath);
			if($json_data = @file_get_contents($s_filepath)){
				// including the datastore file worked, we have the datastores variable now set in memory
				$this->{$s_datastore_name} = json_decode($json_data);
			}
			else{
				// $this->$s_datastore_name = ?
				$this->_create_datestore_afresh($s_datastore_name);
			}
		}
		function initiate_item($_id){
			$s_filepath = S_BASE_PATH.'flot-admin/datastore/'.$_id.'.php';
			clearstatcache(true, $s_filepath);
			if($json_data = file_get_contents($s_filepath)){
				// including the datastore file worked, we have the datastores variable now set in memory
				$this->oa_individual_items[$_id] = array();
				$this->oa_individual_items[$_id] = json_decode($json_data);
			}
		}
		function initiate_element($_id){
			$s_filepath = S_BASE_PATH.'flot-admin/datastore/'.$_id.'.php';
			clearstatcache(true, $s_filepath);
			if($json_data = file_get_contents($s_filepath)){
				// including the datastore file worked, we have the datastores variable now set in memory
				$this->oa_individual_elements[$_id] = array();
				$this->oa_individual_elements[$_id] = json_decode($json_data);
			}
		}

		function _create_datestore_afresh($s_name){
			
			switch($s_name){
				case 'users':
					$this->users = '[]';
					break;
				case 'items':
					$this->items = '[{"id":"pagestart","title":"Welcome","description":"","keywords":"", "parent":"","url":"index.html","template":"template.html","url_auto":"false","oncology":"page","author":"flot","published":"true","date_modified":"10-05-2014"}]';
					$this->oa_individual_items['pagestart'] = json_decode('{"content_html":"<p>Hello, welcome to flot<\/p>\r\n\r\n<p>To get started, <a href=\"\/flot-admin\/admin\/\">\/log in<\/a> with the email and password you used to start flot.<\/p>\r\n\r\n<p>Once logged in you can delete or change this page, and add more.<\/p>\r\n"}');
					$this->b_save_item('pagestart');
					break;
				case 'elements':
					$s_footer_id = uniqid("element");
					$this->elements = '[{"id":"'.$s_footer_id.'","title":"Footer","author":"flot","published":"true","date_modified":"10-05-2014"}]';
					$this->oa_individual_elements[$s_footer_id] = json_decode('{"content_html":"a <a href=\"http:\/\/flot.io\">flot<\/a> website | <a href=\"\/flot-admin\/admin\/\">login<\/a>"}');
					$this->b_save_element($s_footer_id);
					break;
				case 'menus':
					$this->menus = '[{"id":"top_menu","title":"top menu","serialisation":"root:pagestart"}]';
					break;
				case 'pictures':
					$this->pictures = '[]';
					break;
				case 'file_tags':
					$this->file_tags = '{}';
					break;
				case 'urls':
					$this->urls = '[]';
					break;
				case 'settings':
					$this->settings = 
						'{
							"theme":"html5",
							"site_name":"website name",
							"upload_dir":"flot-admin/uploads/",
							"thumb_sizes":
							[
								{
									"name": "large",
									"max_height": "1100"
								},
								{
									"name": "medium",
									"max_height": "300"
								},
								{
									"name": "small",
									"max_width": "115",
									"max_height": "115"
								},
								{
									"name": "tiny",
									"max_width": "32",
									"max_height": "32"
								}
							]
						}';
					break;
				case 'oncologies':
					$this->oncologies = '[
							{
								"id":"page",
								"title":"webpage",
								"elements": ["title", "keywords", "description", "parent", "url", "published", "url_auto", "template", "author", "date_modified", "oncology"],
								"full_elements":
								{
									"content_html":
									{
						                "name": "content",
						                "type": "html",
						                "editable": "true",
						                "tab": "1",
						                "position": "1"
						            }
						        },
								"tabs": {"1": "edit", "2": "extra"},
								"editable": "false"
							}
						]';
					break;
				default:
					$this->$s_name = '[]';
					break;
			}
			$this->{$s_name} = json_decode($this->{$s_name});
			$this->b_save_datastore($s_name);
		}

		function get_current_url_data()
		{
			foreach ($this->urls as $url) {
				if ($url->url == $_SERVER['REQUEST_URI'])
					return $url;
			}
			return false;
		}
		function get_oncology($s_oncology_name)
		{
			foreach ($this->oncologies as $oncology) {
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
		function get_element_data($element_id)
		{
			foreach ($this->elements as $element) {
				if ($element->id === $element_id)
					return $element;
			}
			return false;
		}
		function get_oncology_data($id)
		{
			foreach ($this->oncologies as $oncology) {
				if ($oncology->id === $id)
					return $oncology;
			}
			return false;
		}
		function o_get_full_item($item_id)
		{
			$this->initiate_item($item_id);

			if(isset($this->oa_individual_items[$item_id])){
				return $this->oa_individual_items[$item_id];
			}
			return null;
		}
		function o_get_full_element($element_id)
		{
			$this->initiate_element($element_id);

			if(isset($this->oa_individual_elements[$element_id])){
				return $this->oa_individual_elements[$element_id];
			}
			return null;
		}
		function get_menu_data($menu_id)
		{
			foreach ($this->menus as $menu) {
				if ($menu->id === $menu_id)
					return $menu;
			}
			return false;
		}
		function get_menu_data_from_name($menu_name)
		{
			foreach ($this->menus as $menu) {
				if ($menu->title === $menu_name)
					return $menu;
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
		function s_get_current_user()
		{
			return $this->o_get_user_data($_SESSION['admin_user']);
		}
		function sa_get_pages_except($s_page_id){
			$oa_pages = array();

			foreach ($this->items as $o_item) {
				if($o_item->id !== $s_page_id){
					$oa_pages[$o_item->id] = $o_item->title;
				}
			}

			return $oa_pages;
		}

		function oa_search_pictures($s_query){
			$sa_matching_pictures = array();
			if(!empty($s_query)){
				// search exact file tags
				if(isset($this->file_tags->$s_query)){
					foreach ($this->file_tags->$s_query as $s_result) {
						array_push($sa_matching_pictures, $s_result);
					}
				}

				// search partial
				/*
				if(empty($sa_matching_pictures)){
					// no exact matches, try partials
					print_r($this->file_tags);
					$sa_matching_indexes = preg_grep("/{$s_query}/i", array_keys($this->file_tags));
					print_r($sa_matching_indexes);
					foreach($sa_matching_indexes as $s_index){
						array_push($sa_matching_pictures, $this->pictures[$s_index]);
					}
				}
				*/
			}else{
				// empty search query, add all pictures to return
				foreach ($this->pictures as $o_picture) {
					array_push($sa_matching_pictures, $o_picture->filename);
				}
			}
			// return all
			return $sa_matching_pictures;
		}

		#
		# Setting
		#
		function _set_item_data($new_item)
		{
			for($c_item = 0; $c_item < count($this->items); $c_item++) {
				if ($this->items[$c_item]->id === $new_item->id){
					// set some data in item and some in single full item object
					$this->items[$c_item] = $new_item;
				}
			}
		}
		function _set_element_data($new_element)
		{
			for($c_element = 0; $c_element < count($this->elements); $c_element++) {
				if ($this->elements[$c_element]->id === $new_element->id){
					// set some data in item and some in single full item object
					$this->elements[$c_element] = $new_element;
				}
			}
		}
		function _set_oncology_data($new_oncology)
		{
			for($c_oncology = 0; $c_oncology < count($this->oncologies); $c_oncology++) {
				if ($this->oncologies[$c_oncology]->id === $new_oncology->id){
					// set some data in item and some in single full item object
					$this->oncologies[$c_oncology] = $new_oncology;
				}
			}
		}
		function _set_menu_data($new_menu)
		{
			for($c_menu = 0; $c_menu < count($this->menus); $c_menu++) {
				if ($this->menus[$c_menu]->id === $new_menu->id){
					$this->menus[$c_menu] = $new_menu;
				}
			}
		}
		function s_new_item($s_oncology){
			# create a new item
			$s_new_id = uniqid($s_oncology);
			$odOD = new OncologyData;
			$s_oncology_name = $odOD->s_oncology_name_from_id($s_oncology);
			$s_item_template = '{"id":"'.$s_new_id.'", "keywords":"", "description":"", "parent":"", "title":"new '.$s_oncology_name.'", "url":"/new-'.$s_oncology_name.'/","url_auto":"true", "template":"template.html","oncology":"'.$s_oncology.'", "author":"'.$this->s_get_current_user()->user.'", "published": "false", "date_modified": ""}';
			$s_full_item_template = '{"content_html":""}';

			array_push($this->items, json_decode($s_item_template));
			$this->oa_individual_items[$s_new_id] = json_decode($s_full_item_template);

			# save it to datastore
			$this->b_save_datastore("items");
			$this->b_save_item($s_new_id);

			# return its id
			return $s_new_id;
		}
		function s_new_element(){
			# create a new item
			$s_new_id = uniqid("element");


			$s_element_template = '{"id":"'.$s_new_id.'", "title":"new element", "author":"'.$this->s_get_current_user()->user.'", "published": "true", "date_modified": ""}';
			$s_full_element_template = '{"content_html":""}';

			array_push($this->elements, json_decode($s_element_template));
			$this->oa_individual_elements[$s_new_id] = json_decode($s_full_element_template);

			# save it to datastore
			$this->b_save_datastore("elements");
			$this->b_save_element($s_new_id);

			# return its id
			return $s_new_id;
		}
		function s_new_menu(){
			# create a new item
			$s_new_id = uniqid("menu");
			$s_menu_template = '{"id":"'.$s_new_id.'", "title":"new menu"}';
			array_push($this->menus, json_decode($s_menu_template));

			# save it to datastore
			$this->b_save_datastore("menus");

			# return its id
			return $s_new_id;
		}
		function s_new_oncology(){
			# create a new oncology
			$s_new_id = uniqid("oncology");
			$s_oncology_template = '{"id":"'.$s_new_id.'", "title":"new page type", "elements": ["title", "keywords", "description", "parent", "url", "published", "url_auto", "template", "author", "date_modified", "oncology"], "editable": "true"}';
			array_push($this->oncologies, json_decode($s_oncology_template));

			# save it to datastore
			$this->b_save_datastore("oncologies");

			# return its id
			return $s_new_id;
		}
		function _delete_item($s_id){
			$i_kill_index = -1;
			for($c_item = 0; $c_item < count($this->items); $c_item++) {
				if ($this->items[$c_item]->id === $s_id)
					$i_kill_index = $c_item;
			}
			$this->_delete_full_item($s_id);
			if($i_kill_index > -1){
				unset($this->items[$i_kill_index]);
				$this->items = array_values($this->items);
			}

			$this->b_save_datastore("items");
		}
		function _delete_element($s_id){
			$i_kill_index = -1;
			for($c_element = 0; $c_element < count($this->elements); $c_element++) {
				if ($this->elements[$c_element]->id === $s_id)
					$i_kill_index = $c_element;
			}
			$this->_delete_full_item($s_id);
			if($i_kill_index > -1){
				unset($this->elements[$i_kill_index]);
				$this->elements = array_values($this->elements);
			}

			$this->b_save_datastore("elements");
		}
		function _delete_oncology($s_id){
			$i_kill_index = -1;
			for($c_oncology = 0; $c_oncology < count($this->oncologies); $c_oncology++) {
				if ($this->oncologies[$c_oncology]->id === $s_id)
					$i_kill_index = $c_oncology;
			}
			if($i_kill_index > -1){
				unset($this->oncologies[$i_kill_index]);
				$this->oncologies = array_values($this->oncologies);
			}

			$this->b_save_datastore("oncologies");
		}
		function _delete_full_item($s_id){
			$s_file_path = S_BASE_PATH.'flot-admin/datastore/'.$s_id.'.php';
			@unlink($s_file_path);
		}
		function _delete_menu($s_id){
			$i_kill_index = -1;
			for($c_menu = 0; $c_menu < count($this->menus); $c_menu++) {
				if ($this->menus[$c_menu]->id === $s_id)
					$i_kill_index = $c_menu;
			}
			if($i_kill_index > -1){
				unset($this->menus[$i_kill_index]);
				$this->menus = array_values($this->menus);
			}

			$this->b_save_datastore("menus");
		}
		function b_add_user($s_email, $s_pass){
			try{
				$s_user_template = '{"user":"'.$s_email.'", "pass": "'.$s_pass.'"}';
				array_push($this->users, json_decode($s_user_template));
			}catch(Exception $e){
				echo $e;
			}

			return $this->b_save_datastore("users");
		}

		function _add_file($s_filename){
			# create a new item
			$s_new_id = uniqid("pictures");
			$s_picture_template = '{"id":"'.$s_new_id.'", "filename":"'.$s_filename.'"}';
			array_push($this->pictures, json_decode($s_picture_template));

			# save it to datastore
			$this->b_save_datastore("pictures");
		}
		function _add_file_tags($s_filename, $sa_tags){
			// set each tag with the filename, if there are filename already, add this one
			
			foreach ($sa_tags as $s_tag) {
				$s_tag = strtolower($s_tag);
				if(isset($this->file_tags)){
					if(!isset($this->file_tags->$s_tag)){
						$this->file_tags->$s_tag = array();
					}
				}
				array_push($this->file_tags->$s_tag, $s_filename);
			}
		}

		
		#
		# Saving
		#
		function b_save_datastore($s_datastore){
			if($s_datastore === 'items' ||
				$s_datastore === 'elements' ||
				$s_datastore === 'menus' ||
				$s_datastore === 'users' ||
				$s_datastore === 'pictures' ||
				$s_datastore === 'file_tags' ||
				$s_datastore === 'settings' ||
				$s_datastore === 'urls' ||
				$s_datastore === 'oncologies'){

				$s_write_path = S_BASE_PATH.'flot-admin/datastore/'.$s_datastore.'.php';

				$s_new_content = json_encode($this->{$s_datastore});

				if(@file_put_contents($s_write_path, $s_new_content) > 0){
					return true;
				}else{
					return false;
				}
			}
			return false;
		}
		function b_save_item($s_id)
		{
			// $this->oa_individual_items[$s_id]
			$s_write_path = S_BASE_PATH.'flot-admin/datastore/'.$s_id.'.php';

			if(!isset($this->oa_individual_items[$s_id])){
				echo "individual item not defined";
			}else{

				$s_new_content = json_encode($this->oa_individual_items[$s_id]);

				if(@file_put_contents($s_write_path, $s_new_content) > 0){
					return true;
				}else{
					//error_log("flot couldn't write in the datastore.. :(");
					return false;
				}
			}
			// still here? something went wrong, return false
			return false;
		}
		function b_save_element($s_id)
		{
			// $this->oa_individual_items[$s_id]
			$s_write_path = S_BASE_PATH.'flot-admin/datastore/'.$s_id.'.php';

			if(!isset($this->oa_individual_elements[$s_id])){
				echo "individual element not defined";
			}else{

				$s_new_content = json_encode($this->oa_individual_elements[$s_id]);

				if(@file_put_contents($s_write_path, $s_new_content) > 0){
					return true;
				}else{
					//error_log("flot couldn't write in the datastore.. :(");
					return false;
				}
			}
			// still here? something went wrong, return false
			return false;
		}
		function sa_templates_available(){
			// look up all template files in theme dir
			$sa_files = glob(S_BASE_PATH.'/flot-admin/themes/'.$this->settings->theme.'/*.html');

			foreach ($sa_files as $key => $file) {
				$sa_files[$key] = substr($file, strrpos($file, '/')+1, strlen($file));
			}
			return $sa_files;
		}
	}
?>