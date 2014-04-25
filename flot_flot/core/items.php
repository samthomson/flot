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

			$item_url = new ItemURL($this->o_loaded_item_object);


			# create any directories for the file if neccesary
			if($item_url->has_dirs()){
				# make dirs
				if(!file_exists($this->s_base_path.$item_url->dir_path()))
					mkdir($this->s_base_path.$item_url->dir_path(), 0777, true);
			}

			# write the file itself
			file_put_contents($item_url->writing_file_path($this->s_base_path), $this->html_page);
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


			# store to disk
			$this->update();
		}

		function save(){
			# update the datastore

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

			$html_form .= '<form role="form" method="post" action="index.php">';

			#
			# make tabs
			#
/**/
			# tab menu
			$html_form .= '<ul class="nav nav-tabs">';
			$html_form .= '<li class="active"><a href="#edit" data-toggle="tab">edit</a></li>';
			$html_form .= '<li><a href="#extra" data-toggle="tab">Extra</a></li>';    
			$html_form .= '</ul>';

			# tabs
			$html_form .= '<div class="tab-content">';

			# edit tab
			$html_form .= '<div class="tab-pane active" id="edit">';

			# title
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<input type="text" class="form-control" name="title" placeholder="page title" value="'.urldecode($this->o_loaded_item_object->title).'">';
			$html_form .= '</div>';

			$html_form .= '<div id="medium_editor" oninput="editor_update()" name="content" class="editable">'.urldecode($this->o_loaded_item_object->content_html).'</div>';

			/*

			# content edit
			$html_form .= '<div class="form-group">';
			$html_form .= '<div class="col-xs-6 ">';
			//$html_form .= '<textarea id="item_content_edit" oninput="this.editor.update()" class="form-control" name="content" rows="12">'.urldecode($this->o_loaded_item_object->content).'</textarea>';
			$html_form .= '</div>';

			# content preview
			$html_form .= '<div class="col-xs-6"><div id="item_content_preview">preview here</div>';
			$html_form .= '</div>';
			$html_form .= '</div>';
			*/
			/**/

			# end edit tab
			$html_form .= '</div>';

			# 'extra' tab
			$html_form .= '<div class="tab-pane" id="extra">';
/**/
			# keywords
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<input type="text" class="form-control" name="keywords" placeholder="keywords" value="'.urldecode($this->o_loaded_item_object->keywords).'">';
			$html_form .= '</div>';

			# description
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<input type="text" class="form-control" name="description" placeholder="description" value="'.urldecode($this->o_loaded_item_object->description).'">';
			$html_form .= '</div>';

			# url
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<input type="text" class="form-control" name="url" placeholder="url" value="'.urldecode($this->o_loaded_item_object->url).'">';
			$html_form .= '</div>';


			# published
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<input type="text" class="form-control" name="published" placeholder="true/false" value="'.urldecode($this->o_loaded_item_object->published).'">';
			$html_form .= '</div>';


			# end extra tab
			$html_form .= '</div>';

			# end tabs
			$html_form .= '</div>';
/**/
			# hidden elements

			$html_form .= '<input type="hidden" name="section" value="items">';
			$html_form .= '<input id="content_html" type="hidden" name="content_html" value="items">';
			$html_form .= '<input type="hidden" name="item_id" value="'.urldecode($this->o_loaded_item_object->id).'">';

			# save
			$html_form .= '<div class="form-group">';

			$html_form .= '<div class="col-xs-6 ">';
			$html_form .= '<input value="cancel" class="form-control btn btn-primary">';
			$html_form .= '</div>';

			/*
			$html_form .= '<div class="col-xs-4 ">';
			$html_form .= '<input value="save draft" class="form-control btn btn-warning">';
			$html_form .= '</div>';
			*/

			$html_form .= '<div class="col-xs-6 ">';
			$html_form .= '<input value="Publish / Update" type="submit" class="form-control btn btn-success">';
			$html_form .= '</div>';
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