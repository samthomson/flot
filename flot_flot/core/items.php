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
		function _set_full_item($o_full_item){
			// full item object will have stuff like content, keywords, description, etc
			// loop through each property and add it to exisiting item object
			foreach ($o_full_item as $key => $value) {
			    $this->o_loaded_item_object->$key = $value;
			}
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
				$this->delete();
			}
		}
		function delete() {
			$item_url = new ItemURL($this->o_loaded_item_object);
			# delete the file
			if(file_exists($item_url->writing_file_path($this->s_base_path)))
				unlink($item_url->writing_file_path($this->s_base_path));

			# if it was the last file in folder, delete folder, repeat this recursively until back to root
		}
		function render() {
			# get template
			$template = file_get_contents($this->s_base_path.'/flot_flot/themes/'.$this->datastore->settings->theme.'/template.html');

			# parse in data
			$sa_keys = array_keys(get_object_vars($this->o_loaded_item_object));

			foreach ($sa_keys as $key) {
				if($this->o_loaded_item_object->$key !== null)
					$template = str_replace("{{item:".$key."}}", urldecode($this->o_loaded_item_object->$key), $template);
			}

			# parse in menus
			// look for {{menu:[menu_id]}}
			$template = preg_replace_callback("(\{{menu:(.*?)\}})is", 
				function($m){
					if(isset($m[1])){
						$o_Menu = new Menu($this->datastore->get_menu_data($m[1]));
						return $o_Menu->render();
					}
				},
				$template);

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
			# re-render the page into internal memory
			$this->render();

			# persist the page to disk
			$this->update();
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
			$s_id = urldecode($this->o_loaded_item_object->id);
			$s_title = urldecode($this->o_loaded_item_object->title);
			$s_url = urldecode($this->o_loaded_item_object->url);
			$s_content_html = urldecode($this->o_loaded_item_object->content_html);
			$s_keywords = urldecode($this->o_loaded_item_object->keywords);
			$s_description = urldecode($this->o_loaded_item_object->description);
			$s_template = urldecode($this->o_loaded_item_object->template);
			$s_title = urldecode($this->o_loaded_item_object->title);
			$b_published = urldecode($this->o_loaded_item_object->published);
			$s_checked = urldecode($this->o_loaded_item_object->url_auto);

			$s_published_class = "";
			$s_unpublished_class = "";
			$s_url_input_disabled = "";

			if($b_published === "true")
				$s_published_class = "disabled ";
			else
				$s_unpublished_class = "disabled ";

			if($s_checked === "true"){
				$s_checked = " checked";
				$s_url_input_disabled = " disabled";
			}

			# start button group
			$html_form .= '<div class="btn-group" id="edit_item_general_toolbar">';

			$html_form .= '<a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-expand"></i><span class="small-hidden"> preview</span></a>';
			
			$html_form .= '<a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-fire"></i><span class="small-hidden"> purge from cache</span></a>';

			$html_form .= '<a class="btn btn-default btn-sm" href="/flot_flot/admin/index.php?section=items&oncology=page&item='.$s_id.'&action=delete"><i class="glyphicon glyphicon-trash"></i><span class="small-hidden"> delete</span></a>';

			$html_form .= '<a target="_blank" '.$s_unpublished_class.'class="btn btn-default btn-sm" href="'.$s_url.'"><i class="glyphicon glyphicon-eye-open"></i><span class="small-hidden"> view</span></a>';

			# end button group
			$html_form .= '</div>';



			# published toggle


			$html_form .= '<div class="btn-group" id="edit_item_publish_toolbar">';
			$html_form .= '<a class="btn btn-default btn-sm" '.$s_published_class.'href="javascript:publish(\'published\');"><i class="glyphicon glyphicon-cloud-download"></i> save & publish on the internet</a>';		
			$html_form .= '<a class="btn btn-default btn-sm" '.$s_unpublished_class.'href="javascript:publish(\'unpublished\');"><i class="glyphicon glyphicon-cloud-upload"></i> unpublish from the internet</a>';			
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
			$html_form .= '<label for="item_keywords">Title</label><input type="text" class="form-control" name="title" placeholder="page title" value="'.$s_title.'" id="item_edit_title">';
			$html_form .= '</div>';

			# url
			/*
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="item_keywords">Web address (URL)</label><input type="text" class="form-control" name="url" placeholder="url" value="'.$s_url.'">';
			$html_form .= '</div>';
			*/

			$html_form .= '<div class="input-group input-group-sm">';
			$html_form .= '<span class="input-group-addon">';
        	$html_form .= '<input id="item_edit_auto_url" name="url_auto" '.$s_checked.' value="true" type="checkbox"> set url for me';
      		$html_form .= '</span>';
      		$html_form .= '<input type="text" id="item_edit_url" class="form-control item_edit_url" placeholder="relative url" value="'.$s_url.'"'.$s_url_input_disabled.'>';
    		$html_form .= '</div><!-- /input-group -->';

			# editor
			$html_form .= '<hr/><label class="form-group">WYSIWYG editer</label><br/>';
			$html_form .= '<textarea id="wysiwyg_editor" name="content_html">'.$s_content_html.'</textarea><br/>';


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

			# template
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="item_template">Template</label><input type="text" class="form-control" name="template" id="item_template" placeholder="template" value="'.$s_template.'">';
			$html_form .= '</div>';

			# end extra tab
			$html_form .= '</div>';

			# end tabs
			$html_form .= '</div>';

			# hidden elements
			$html_form .= '<input id="published" type="hidden" name="published" value="'.$b_published .'">';
			$html_form .= '<input type="hidden" name="section" value="items">';
			$html_form .= '<input type="hidden" class="item_edit_url" name="url" value="'.$s_url.'">';
			$html_form .= '<input type="hidden" name="item_id" value="'.$s_id.'">';

			# save
			$html_form .= '<div class="form-group">';

			$html_form .= '<input value="save" type="submit" class="form-control btn btn-success">';
			$html_form .= '</div>';

			$html_form .= '</form>';

			$html_form .= '<div id="file_browser_modal" class="modal fade">
			  <div class="container">
			    <div class="modal-content">			    	
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        <h4 class="modal-title">Select a picture to insert</h4>
			      </div>
			      <div class="modal-body">
			      	Click a file to select it, you can upload new files too. Once files are selected you can click "insert pictures" or choose a different picture size from the drop up menu on the same button.<hr/>';

				$o_FileBrowser = new FileBrowser("select");

				$html_form .= $o_FileBrowser->html_make_browser();


			$html_form .= '</div>
			      <div class="modal-footer">
			      <div id="file_browser_selected"></div><hr/>
			        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			        
			        <div class="btn-group dropup">
				        <button id="file_browser_insert_selected" onclick="insert_selected_pictures(\''.$this->datastore->settings->upload_dir.'\', \'medium\')" type="button" class="disabled btn btn-success">Insert picture(s)</button>
				        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
				          <span class="caret"></span>
				          <span class="sr-only">Toggle Dropdown</span>
				        </button>
				        <ul class="dropdown-menu" role="menu">
				        ';

				        foreach ($this->datastore->settings->thumb_sizes as $size) {
				        	$html_form .= '<li><a href="javascript:insert_selected_pictures(\''.$this->datastore->settings->upload_dir.'\', \''.$size->name.'\');">'.$size->name.'</a></li>';
				        }
				        $html_form .= '<li><a href="javascript:insert_selected_pictures(\''.$this->datastore->settings->upload_dir.'\', \'\');">original</a></li>
				        </ul>
				      </div>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->';

			return $html_form;
		}
		function update_from_post(){
			# update the item from post variables
			# we can find out what post variables to look for by checking our oncology
			$flot = new Flot();
			$flot->b_is_user_admin();
			// set url auto to false as a default, since it will only be posted if it was checked
			$this->o_loaded_item_object->url_auto = "false";
			$this->o_loaded_item_object->url= "";

			foreach($this->o_oncology->elements as $element){
				$s_new_value = $flot->s_post_var($element, false);
				if($s_new_value){
					$this->o_loaded_item_object->$element = urldecode($s_new_value);
				}
			}
			$this->datastore->o_loaded_item_object->oa_individual_items[$this->o_loaded_item_object->id] = [];

			foreach($this->o_oncology->full_elements as $element){
				$s_new_value = $flot->s_post_var($element, false);
				if($s_new_value){
					$this->datastore->oa_individual_items[$this->o_loaded_item_object->id]->$element = urldecode($s_new_value);
				}
			}
			# update date and set author
			//$this->o_loaded_item_object->date_modified = date("D jS M Y");
			$this->o_loaded_item_object->date_modified = date("d-m-Y");
			$this->o_loaded_item_object->author = $flot->s_current_user;

			$this->datastore->_set_item_data($this->o_loaded_item_object);
			$this->datastore->b_save_datastore("items");
			$this->datastore->b_save_item($this->o_loaded_item_object->id);
		}
	}
?>