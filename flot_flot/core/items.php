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
		public $o_oncology;
		public $datastore;

		function __construct($o_item) {
			$this->o_loaded_item_object = $o_item;
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			# set a reference to my oncology
			$this->datastore = new DataStore;
			$this->o_oncology = $this->datastore->get_oncology($o_item->oncology);
		}

		function rebuild() {
			# render, and rebuild dependent items
		}
		function update() {
			# physical file storing of page; create new from render, or delete if unpublished

			$item_url = new ItemURL($this->o_loaded_item_object);

			if($this->o_loaded_item_object->published === "true")
			{
				# create any directories for the file if neccesary
				if($item_url->has_dirs()){
					# make dirs
					if(!file_exists($this->s_base_path.$item_url->dir_path()))
						mkdir($this->s_base_path.$item_url->dir_path(), 0777, true);
				}

				# write the file itself
				file_put_contents($item_url->writing_file_path($this->s_base_path), $this->html_page);
			}else{
				// the item is not marked as 'published' so we don't want it saved, or there to be a saved copy of the redndered webpage
			}
		}
		function delete() {
			# delete the file

			# if it was the last file in folder, delete folder, repeat this recursively until back to root
		}
		function render() {
			# get template
			$template = file_get_contents($this->s_base_path.'/flot_flot/themes/'.$this->datastore->settings->theme.'/flot_template.html');

			# parse in data
			$sa_keys = array_keys(get_object_vars($this->o_loaded_item_object));

			foreach ($sa_keys as $key) {
				if($this->o_loaded_item_object->$key !== null)
					$template = str_replace("{{item:".$key."}}", urldecode($this->o_loaded_item_object->$key), $template);
			}
			# general parsing
			$template = str_replace("{{flot:theme_dir}}", '/flot_flot/themes/'.$this->datastore->settings->theme.'/', $template);

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

			//ob_start("ob_gzhandler");
			$this->html_page = $template;
		}

		function save(){
			# update the datastore
			$this->update();

			# re-render the page
			$this->render();
		}

		#
		# content generation
		#
		function make_header(){
			# spit out content type (settings? content type, or not to display)

			# keywords etc, generate if necessary

			# open graph stuff
		}

		#
		# editing
		#
		function html_edit_form(){
			$html_form = "";
			$s_title = urldecode($this->o_loaded_item_object->title);
			$s_url = urldecode($this->o_loaded_item_object->url);
			$s_content_html = urldecode($this->o_loaded_item_object->content_html);
			$s_keywords = urldecode($this->o_loaded_item_object->keywords);
			$s_description = urldecode($this->o_loaded_item_object->description);
			$s_title = urldecode($this->o_loaded_item_object->title);
			$b_published = urldecode($this->o_loaded_item_object->published);

			$s_published_class = "";
			$s_unpublished_class = "";

			if($b_published === "true")
				$s_published_class = "disabled ";
			else
				$s_unpublished_class = "disabled ";

			$html_form .= '<div class="btn-group" id="edit_item_general_toolbar"><a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-expand"></i><span class="small-hidden"> preview</span></a>';

			$html_form .= '<a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-refresh"></i><span class="small-hidden"> regenerate</span></a>';
			
			$html_form .= '<a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-fire"></i><span class="small-hidden"> purge from cache</span></a>';

			$html_form .= '<a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-trash"></i><span class="small-hidden"> delete</span></a></div>';



			# published toggle


			$html_form .= '<div class="btn-group" id="edit_item_publish_toolbar">';
			$html_form .= '<a class="btn btn-default btn-sm" '.$s_published_class.'href="javascript:publish(\'published\');">publish on the internet</a>';		
			$html_form .= '<a class="btn btn-default btn-sm" '.$s_unpublished_class.'href="javascript:publish(\'unpublished\');">unpublish from the internet</a>';			
			$html_form .= '</div><div id="publish_output"></div><hr/>';

			$html_form .= '<form id="item_edit_form" role="form" method="post" action="index.php">';

			#
			# make tabs
			#

			# tab menu
			$html_form .= '<ul class="nav nav-tabs">';
			$html_form .= '<li class="active"><a href="#edit" data-toggle="tab">edit</a></li>';
			$html_form .= '<li><a href="#extra" data-toggle="tab">Extra</a></li>';    
			$html_form .= '</ul>';

			# tabs
			$html_form .= '<div class="tab-content">';

			# 
			# edit tab
			#
			$html_form .= '<div class="tab-pane active" id="edit">';

			# title
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="item_keywords">Title</label><input type="text" class="form-control" name="title" placeholder="page title" value="'.$s_title.'">';
			$html_form .= '</div>';

			# url
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="item_keywords">Web address (URL)</label><input disabled type="text" class="form-control" name="url" placeholder="url" value="'.$s_url.'">';
			$html_form .= '</div>';

			# editor
			$html_form .= '<label>WYSIWYG editer</label><div id="medium_editor" oninput="editor_update()" class="editable">'.$s_content_html.'</div>';


			# end edit tab
			$html_form .= '</div>';

			#
			# 'extra' tab
			#
			$html_form .= '<div class="tab-pane" id="extra">';

			# keywords
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="item_keywords">Keywords (comma seperated)</label><input id="item_keywords" type="text" class="form-control" name="keywords" placeholder="keywords" value="'.$s_keywords.'">';
			$html_form .= '</div>';

			# description
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="item_description">Description</label><input type="text" class="form-control" name="description" id="item_description" placeholder="description" value="'.$s_description.'">';
			$html_form .= '</div>';

			# end extra tab
			$html_form .= '</div>';

			# end tabs
			$html_form .= '</div>';

			# hidden elements
			$html_form .= '<input id="published" type="hidden" name="published" value="'.$b_published .'">';
			$html_form .= '<input type="hidden" name="section" value="items">';
			$html_form .= '<input id="content_html" type="hidden" name="content_html" value="items">';
			$html_form .= '<input type="hidden" name="item_id" value="'.urldecode($this->o_loaded_item_object->id).'">';

			# save
			$html_form .= '<div class="form-group">';

			$html_form .= '<input value="save" type="submit" class="form-control btn btn-default">';
			$html_form .= '</div>';

			$html_form .= '</form>';

			return $html_form;
		}
		function update_from_post(){
			# update the item from post variables
			# we can find out what post variables to look for by checking our oncology
			$flot = new Flot();
			foreach($this->o_oncology->elements as $element){
				$s_new_value = $flot->s_post_var($element, false);
				if($s_new_value){
					$this->o_loaded_item_object->$element = urlencode($s_new_value);
				}
			}
			$this->datastore->_set_item_data($this->o_loaded_item_object);
			$this->datastore->_save_datastore("items");
		}
	}
?>