<?php
	# handles everything to do with the items, initiating and rendering

	# properties: url, private, oncology, template, dynamic/static
	# methods: rebuild, update, add, edit, delete


    # initiate an item from the data in urls datastore
	
	# call its render method

	class Item {

		public $o_loaded_item_object;
		public $html_page;
		public $s_base_path;

		function __construct($o_item, $s_base_path) {
			$this->o_loaded_item_object = $o_item;
			$this->s_base_path = $s_base_path;
		}

		function rebuild() {
			# render, and rebuild dependent items
		}
		function update() {

			$current_url = new CurrentURL;

			# create any directories for the file if neccesary
			if($current_url->has_dirs()){
				# make dirs
				mkdir($this->s_base_path.$current_url->dir_path(), 0777, true);
			}

			# write the file itself	
			echo "write the file: ".$current_url->writing_file_path($this->s_base_path);
			file_put_contents($current_url->writing_file_path($this->s_base_path), $this->html_page);




/*
			$s_file_base = "";
			$s_file_name = "";
			if(!strpos($this->o_loaded_item_object->url, '.') || strrpos($this->o_loaded_item_object->url, '/') > -1){
				# the path has directories in it
				$s_file_base = $this->o_loaded_item_object->url; // one dir
				if(strrpos($this->o_loaded_item_object->url, '/') > -1){ #multiple dirs
					$s_file_base = substr($this->o_loaded_item_object->url, 0, strrpos($this->o_loaded_item_object->url, '/'));
				}
				echo "make dir: $s_file_base<br/>";
				mkdir($this->s_base_path.$s_file_base, 0777, true);
			}
			$s_file_name = $this->s_base_path.$this->o_loaded_item_object->url;
			if(!strpos($s_file_name, '.')){
				# no file name, is dir, write as index
				$s_file_name .= '/index.html';
			}
			echo $s_file_name;
			file_put_contents($s_file_name, $this->html_page);
			*/
		}
		function delete() {
			# delete the file

			# if it was the last file in folder, delete folder, repeat this recursively until back to root
		}
		function render() {
			# get template
			$template = file_get_contents('themes/first_theme/page.html');

			# parse in data
			$sa_keys = array_keys(get_object_vars($this->o_loaded_item_object));

			foreach ($sa_keys as $key) {
				if($this->o_loaded_item_object->$key !== null)
					$template = str_replace("{{item:".$key."}}", $this->o_loaded_item_object->$key, $template);
			}
			# minify etc
			$search = array(
		        '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
		        '/[^\S ]+\</s',  // strip whitespaces before tags, except space
		        '/(\s)+/s'       // shorten multiple whitespace sequences
		    );

		    $replace = array(
		        '>',
		        '<',
		        '\\1'
		    );

		    $template = preg_replace($search, $replace, $template);

			# serve to user
			echo $template;
			$this->html_page = $template;

			# store to disk
			$this->update();
		}
	}
?>