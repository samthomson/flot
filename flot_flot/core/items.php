<?php
	# handles everything to do with the items, initiating and rendering

	# properties: url, private, oncology, template, dynamic/static
	# methods: rebuild, update, add, edit, delete


    # initiate an item from the data in urls datastore
	
	# call its render method

	class Item {

		public $o_loaded_item_object;
		public $o_full_item_object;
		public $html_page;
		public $o_oncology;
		public $datastore;

		function __construct($o_item) {
			$this->o_loaded_item_object = $o_item;
			# set a reference to my oncology
			$this->datastore = new DataStore;
			$this->o_oncology = $this->datastore->get_oncology($o_item->oncology);
		}
		function _set_full_item($o_full_item){
			// full item object will have item specific properties, not shared with default items
			// loop through each property and add it to exisiting item object
			$this->o_full_item_object = array();
			foreach ($o_full_item as $key => $value) {
			    $this->o_full_item_object[$key] = $value;
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
					if(!file_exists(S_BASE_PATH.$item_url->dir_path()))
						mkdir(S_BASE_PATH.$item_url->dir_path(), 0777, true);
				}

				# write the file itself
				$fu_FileUtility = new FileUtilities;
				if(!$fu_FileUtility->b_safely_write_file($item_url->writing_file_path(S_BASE_PATH), $this->html_page)){
					// writing failed, set published status to false
					$this->o_loaded_item_object->published = "false";
					$this->datastore->b_save_datastore("items");
				}
				
			}else{
				// the item is not marked as 'published' so we don't want it saved, or there to be a saved copy of the rendered webpage
				$this->delete();
			}
		}
		function delete() {
			$item_url = new ItemURL($this->o_loaded_item_object);
			# delete the file
			$s_writing_file_path = $item_url->writing_file_path(S_BASE_PATH);
			if(file_exists($s_writing_file_path))
				unlink($s_writing_file_path);

			# if it was the last file in folder, delete folder, repeat this recursively until back to root
			$fu_FileUtility = new FileUtilities;
			$s_directory_containing_file = $fu_FileUtility->s_lowest_directory_of_path($s_writing_file_path);
			
			// delete folder if it's empty			
			if($fu_FileUtility->b_is_dir_empty($s_directory_containing_file)){
				rmdir($s_directory_containing_file);
			}
		}
		function render() {
			# get template
			$template = file_get_contents(S_BASE_PATH.'/flot_flot/themes/'.$this->datastore->settings->theme.'/'.$this->o_loaded_item_object->template);

			# parse in data
			$sa_keys = array_keys(get_object_vars($this->o_loaded_item_object));

			// render default item attributes
			foreach ($this->o_oncology->elements as $key) {
				$s_swap_in = '';
				if(isset($this->o_loaded_item_object->$key)){
					$s_swap_in = urldecode($this->o_loaded_item_object->$key);
				}
				$template = str_replace("{{item:".$key."}}", $s_swap_in, $template);
			}
			// render full item attributes
			foreach ($this->o_oncology->full_elements as $key => $value) {
				$s_swap_in = '';
				if(isset($this->o_full_item_object[$key])){
					$s_swap_in = urldecode($this->o_full_item_object[$key]);
				}
				$template = str_replace("{{item:".$key."}}", $s_swap_in, $template);
			}

			# parse in menus
			// look for {{menu:[menu_id]}}
			$template = preg_replace_callback("(\{{menu:(.*?)\}})is", 
				function($m){
					if(isset($m[1])){
						$o_Menu = new Menu($this->datastore->get_menu_data_from_name($m[1]));
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
			return $this->update();
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
			/*
			get the item id and oncology
			build the ui form based on oncology and retrieved item data
			*/
			

			$html_form = "";

			// get default item properties
			$s_id = urldecode($this->o_loaded_item_object->id);
			$s_title = urldecode($this->o_loaded_item_object->title);
			$s_keywords = urldecode($this->o_loaded_item_object->keywords);
			$s_description = urldecode($this->o_loaded_item_object->description);
			$s_parent = urldecode($this->o_loaded_item_object->parent);
			$s_url = urldecode($this->o_loaded_item_object->url);
			$b_published = urldecode($this->o_loaded_item_object->published);
			$s_checked = urldecode($this->o_loaded_item_object->url_auto);
			$s_template = urldecode($this->o_loaded_item_object->template);

			// iterate through oncologies 'full elements'
			foreach($this->o_oncology->full_elements as $key => $value){
				#echo "$key<br/>";
				#print_r($value);
			}
			$s_content_html = '';

			if(isset($this->o_full_item_object['content_html'])){
				$s_content_html = urldecode($this->o_full_item_object['content_html']);
			}

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
			$html_form .= '<div class="btn-group edit_item_general_toolbar">';

			// preview
			$html_form .= '<a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-expand"></i><span class="small-hidden"> preview</span></a>';
			/*
			$html_form .= '<a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-fire"></i><span class="small-hidden"> purge from cache</span></a>';
			*/

			// view (open in new tab)
			$oUrlStuff = new UrlStuff;
			$s_view_url = $oUrlStuff->s_format_url_from_item_url($s_url);

			$html_form .= '<a target="_blank" '.$s_unpublished_class.'class="btn btn-default btn-sm" href="'.$s_view_url.'"><i class="glyphicon glyphicon-eye-open"></i><span class="small-hidden"> view</span></a>';

			# end button group
			$html_form .= '</div>';



			# published toggle
			$html_form .= '<div class="btn-group edit_item_general_toolbar">';
			$html_form .= '<a class="btn btn-success btn-sm" '.$s_published_class.'href="javascript:publish(\'published\');"><i class="glyphicon glyphicon-cloud-upload"></i> save &amp; publish</a>';		
			$html_form .= '<a class="btn btn-warning btn-sm" '.$s_unpublished_class.'href="javascript:publish(\'unpublished\');"><i class="glyphicon glyphicon-cloud-download"></i> unpublish</a>';		
			$html_form .= '</div>';

			// delete button group
			$html_form .= '<div class="btn-group">';
			// delete
			$html_form .= '<a class="btn btn-danger btn-sm" href="/flot_flot/admin/index.php?section=items&oncology=page&item='.$s_id.'&action=delete"><i class="glyphicon glyphicon-trash"></i><span class="small-hidden"> delete</span></a>';		
			$html_form .= '</div>';


			$html_form .= '<div id="publish_output"></div><hr/>';

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
			$html_form .= '<div class="input-group input-group-sm">';
			$html_form .= '<span class="input-group-addon">';
        	$html_form .= '<input id="item_edit_auto_url" name="url_auto" '.$s_checked.' value="true" type="checkbox"> set url for me';
      		$html_form .= '</span>';
      		$html_form .= '<input type="text" id="item_edit_url" class="form-control item_edit_url" placeholder="relative url" value="'.$s_url.'"'.$s_url_input_disabled.'>';
      		//$html_form .= '<span class="input-group-btn"><button class="btn btn-default" type="button"></button></span>';
      		$s_make_home_page_disabled = '';
      		if($s_url === "index.html"){
	      		$s_make_home_page_disabled = ' disabled';
	      	}
      		$html_form .= '<span class="input-group-btn"><a class="btn btn-default btn-sm'.$s_make_home_page_disabled.'" href="javascript:_make_home_page();"><i class="glyphicon glyphicon-home"></i><span class="small-hidden"> make homepage</span></a></span>';
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
			$html_form .= '<label for="item_template">Template</label>';

			$html_form .= '<select name="template" class="form-control" id="item_template">';

			$sa_files = $this->datastore->sa_templates_available();
			foreach ($sa_files as $s_template_file) {
				$s_selected = '';

				if($s_template_file === $s_template){
					$s_selected = 'selected ';
				}
				$html_form .= '<option '.$s_selected.'value="'.$s_template_file.'" >'.$s_template_file.'</option>';
				
			}
			$html_form .= '</select>';
			$html_form .= '</div>';

			# parent
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="item_parent">Parent</label>';

			$html_form .= '<select name="parent" class="form-control" id="item_parent">';
			$html_form .= '<option value=""></option>';

			$oa_partial_items = $this->datastore->sa_get_pages_except($s_id);
			foreach ($oa_partial_items as $item_id => $item_value) {
				$s_selected = '';

				if($item_id === $s_parent){
					$s_selected = 'selected ';
				}
				$html_form .= '<option '.$s_selected.'value="'.$item_id.'" >'.$item_value.'</option>';
				
			}
			$html_form .= '</select>';
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
				// go through all items in oncology
				$s_new_value = $flot->s_post_var($element, false);
				if($s_new_value){
					$this->o_loaded_item_object->$element = urldecode($s_new_value);
				}
			}
			
			# update date and set author
			$this->o_loaded_item_object->date_modified = date("d-m-Y");
			$this->o_loaded_item_object->author = $flot->s_current_user;
			$this->datastore->_set_item_data($this->o_loaded_item_object);
			$this->datastore->b_save_datastore("items");


			//$this->datastore->o_loaded_item_object->oa_individual_items[$this->o_loaded_item_object->id] = [];

			$this->datastore->oa_individual_items[$this->o_loaded_item_object->id] = [];


			foreach($this->o_oncology->full_elements as $element => $properties){
				$s_new_value = $flot->s_post_var($element, false);
				if($s_new_value){
					$this->datastore->oa_individual_items[$this->o_loaded_item_object->id][$element] = urldecode($s_new_value);				
				}
			}

			// save full item, which we just edited directly
			$this->datastore->b_save_item($this->o_loaded_item_object->id);
			// now save the item in amongst others, and messing up the newly set data on the high level item in the process (but we've saved that now)

			// now we have updated the loaded item object, and the individual item object.

			// lets bring the changes to the individual item object, across to the loaded item object, so that any future in memory calls to the item get the propogated changes
			$this->_set_full_item($this->datastore->oa_individual_items[$this->o_loaded_item_object->id]);
		}
	}
?>