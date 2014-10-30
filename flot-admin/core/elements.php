<?php

	class Element {

		public $o_loaded_element_object;
		public $o_full_element_object;
		public $html_page;
		public $datastore;

		function __construct($o_element) {
			$this->o_loaded_element_object = $o_element;
			# set a reference to my oncology
			$this->datastore = new DataStore;
		}
		function _set_full_element($o_full_element){
			$this->o_full_element_object = array();
			foreach ($o_full_element as $key => $value) {
			    $this->o_full_element_object[$key] = $value;
			}
		}
/*
		function rebuild() {
			# render, and rebuild dependent items
		}
		*/

		/*
		function update() {
			# physical file storing of page; create new from render, or delete if unpublished

			$item_url = new ItemURL($this->o_loaded_item_object);

			if($this->o_loaded_item_object->published === "true")
			{
				# create any directories for the file if neccesary
				if($item_url->has_dirs()){
					# make dirs
					if(!file_exists(FLOT_CACHE_PATH.$item_url->dir_path()))
						mkdir(FLOT_CACHE_PATH.$item_url->dir_path());
				}

				# write the file itself
				$fu_FileUtility = new FileUtilities;
				if(!$fu_FileUtility->b_safely_write_file($item_url->writing_file_path(FLOT_CACHE_PATH), $this->html_page)){
					// writing failed, set published status to false
					$this->o_loaded_item_object->published = "false";
					$this->datastore->b_save_datastore("items");
				}
				
			}else{
				// the item is not marked as 'published' so we don't want it saved, or there to be a saved copy of the rendered webpage
				$this->delete();
			}
		}
		*/


		function render() {
			if($this->o_loaded_element_object->published === "false"){
				return '';
			}

			$template = urldecode($this->o_full_element_object['content_html']);


			//$template = preg_replace_callback("(\{{menu:(.*?)\}})is", "s_menu_replace", $template);



		    //$template = preg_replace($search, $replace, $template);

			# serve
			$this->html_page = $template;
			return $this->html_page;
		}

/*
		function save(){
			# re-render the page into internal memory
			$this->render();

			# persist the page to disk
			return $this->update();
		}
		*/

/*
		#
		# content generation
		#
		function make_header(){
			# spit out content type (settings? content type, or not to display)

			# keywords etc, generate if necessary

			# open graph stuff
		}
*/
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
			$s_id = urldecode($this->o_loaded_element_object->id);
			$s_title = urldecode($this->o_loaded_element_object->title);
			$b_published = urldecode($this->o_loaded_element_object->published);
			
			$s_value = urldecode($this->o_full_element_object['content_html']);
			
			
			$s_published_class = "";
			$s_unpublished_class = "";

			if($b_published === "true")
				$s_published_class = "disabled ";
			else
				$s_unpublished_class = "disabled ";

			# start button group
			$html_form .= '<div class="btn-group edit_item_general_toolbar">';


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
			$html_form .= '<a class="disabled btn btn-danger btn-sm" href="/flot-admin/admin/index.php?section=items&oncology=page&item='.$s_id.'&action=delete"><i class="glyphicon glyphicon-trash"></i><span class="small-hidden"> delete</span></a>';		
			$html_form .= '</div>';


			$html_form .= '<div id="publish_output"></div><hr/>';

			$html_form .= '<div class="alert alert-info">Elements can be made and edited in one place, but used throughout your site on multiple pages or put directly in the template. Copy paste the embed code below and flot will replace it with the entered content.</div>';

			$html_form .= '<form id="element_edit_form" role="form" method="post" action="index.php">';

			
			# 
			# edit element
			#
			$html_form .= '<div class="tab-pane active" id="edit">';

			# title
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="item_keywords">Title</label><input type="text" class="form-control" name="title" placeholder="page title" value="'.$s_title.'" id="item_edit_title">';
			$html_form .= '</div>';

			
			$html_form .= '<hr/>';

			//
			// oncology specific elements
			//

			$html_form .= '<textarea id="element_value" class="ckeditor" name="content_html">'.$s_value.'</textarea><br/>';



			// default types
			$html_form .= '<h4>Embed</h4>';
			$html_form .= '<kbd>{{element:'.$s_id.'}}</kbd>';


			$html_form .= '<hr/>';



			# hidden elements
			$html_form .= '<input id="published" type="hidden" name="published" value="'.$b_published .'">';
			$html_form .= '<input type="hidden" name="section" value="elements">';
			$html_form .= '<input type="hidden" name="element_id" value="'.$s_id.'">';

			# save
			$html_form .= '<div class="form-group">';

			$html_form .= '<button type="submit" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-disk"></i> save</button>';
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
			$flot = new Flot();
			$ufUF = new UtilityFunctions;
			$flot->b_is_user_admin();

			
			$s_new_title = $ufUF->s_post_var("title", false);
			$s_new_value = $ufUF->s_post_var("content_html", false);

			
			
			# update date and set author
			$this->o_loaded_element_object->title = $s_new_title;
			$this->o_loaded_element_object->date_modified = date("d-m-Y");
			$this->o_loaded_element_object->author = $flot->s_current_user;
			$this->datastore->_set_element_data($this->o_loaded_element_object);
			$this->datastore->b_save_datastore("elements");

			$this->datastore->oa_individual_elements[$this->o_loaded_element_object->id] = array();

			$this->datastore->oa_individual_elements[$this->o_loaded_element_object->id]['content_html'] = $s_new_value;

			$this->_set_full_element($this->datastore->oa_individual_elements[$this->o_loaded_element_object->id]);

			// save full item, which we just edited directly
			$this->datastore->b_save_element($this->o_loaded_element_object->id);
		}
	}
?>