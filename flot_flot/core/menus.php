<?php
	# menus; initiate, make edit form, render to ui

	class Menu {

		public $datastore;
		public $o_loaded_menu_object;

		function __construct($o_menu) {
			$this->o_loaded_menu_object = $o_menu;
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			# set a reference to my oncology
			$this->datastore = new DataStore;
		}
		function render() {
			# spit out ul
		}

		function save(){
			# re-render the pages using this menu?
			//$this->render();
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
			$s_id = urldecode($this->o_loaded_menu_object->id);
			$s_name = urldecode($this->o_loaded_menu_object->title);


			$html_form .= '<div class="btn-group"><a disabled class="btn btn-default btn-sm" href="#"><i class="glyphicon glyphicon-trash"></i><span class="small-hidden"> delete</span></a></div>';

			$html_form .= '<hr/>';

			$html_form .= '<form id="menu_edit_form" role="form" method="post" action="index.php">';



			# title
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="menu_name">Name</label><input type="text" class="form-control" name="name" placeholder="menu name" value="'.$s_name.'">';
			$html_form .= '</div>';



			$html_form .= '<input type="hidden" name="section" value="menus">';

			$html_form .= '<input type="hidden" name="menu_id" value="'.$s_id.'">';

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

			$s_id = $flot->s_post_var("menu_id", false);

			if($s_id){
				$s_name = $flot->s_post_var("name", false);

				if($s_name)
					$this->o_loaded_menu_object->title = urldecode($s_name);

				$this->datastore->_set_menu_data($this->o_loaded_menu_object);
				$this->datastore->_save_datastore("menus");
			}else{echo "no id";}
		}
	}
?>