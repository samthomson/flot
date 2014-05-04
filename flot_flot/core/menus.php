<?php
	# menus; initiate, make edit form, render to ui

	class Menu {

		public $datastore;
		public $o_loaded_menu_object;
		public $flot;

		function __construct($o_menu) {
			$this->o_loaded_menu_object = $o_menu;
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
			# set a reference to my oncology
			$this->datastore = new DataStore;
			$this->flot = new Flot;
		}
		function render() {
			# spit out ul
			$html_menu = "";

			$html_menu = urldecode($this->o_loaded_menu_object->serialisation);
			
			$html_menu = str_replace('root:', "", $html_menu);
			$html_menu = str_replace(':', "<ul><li>", $html_menu);
			$html_menu = str_replace(';', "</li></ul><ul><li>", $html_menu);
			$html_menu = str_replace(',', "</li><li>", $html_menu);



			return "<ul><li>".$html_menu."</ul>";
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
			// get pages
			$oa_items = $this->flot->oa_pages();
			// go through items and take out and that are in the menu serialisation, leaving only what's available


			$s_id = urldecode($this->o_loaded_menu_object->id);
			$s_name = urldecode($this->o_loaded_menu_object->title);
			$s_serialisation = urldecode($this->o_loaded_menu_object->serialisation);
			
			$html_form = "";


			// push out js to load the menu
			$html_form .= "<script>";

			$sa_menu_levels = explode(';', $s_serialisation);

			$sa_root_menu_items = array();
			foreach ($sa_menu_levels as $s_menu) {
				$sa_menu = explode(':', $s_menu);
				if(count($sa_menu) === 2){

					$s_menu_parent = $sa_menu[0];
					$html_form .= "oa_menus['".$s_menu_parent."'] = [];";

					$sa_menu_items = explode(",", $sa_menu[1]);
					if(count($sa_menu_items) > 0){
						foreach ($sa_menu_items as $s_menu_item) {
							$html_form .= "oa_menus['".$s_menu_parent."'].push('".$s_menu_item."');";
							if($sa_menu[0] === "root")
								array_push($sa_root_menu_items, $s_menu_item);
						}
					}
				}
			}

			$html_form .= "console.log(oa_menus);";
			$html_form .= "</script>";	



			$html_form .= '<div class="btn-group"><a class="btn btn-default btn-sm" href="/flot_flot/admin/index.php?section=menus&menu='.$s_id.'&action=delete"><i class="glyphicon glyphicon-trash"></i><span class="small-hidden"> delete</span></a></div>';

			$html_form .= '<hr/>';

			$html_form .= '<form id="menu_edit_form" role="form" method="post" action="index.php">';



			# title
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="menu_name">Name</label><input type="text" class="form-control" name="name" placeholder="menu name" value="'.$s_name.'">';
			$html_form .= '</div>';

			$html_form .= '<div class="row">';
			$html_form .= '<div class="col-xs-12 col-sm-8">
			<h4>re-order</h4><div id="menu_order_area"><ul class="menu_items_pages">';

			foreach ($sa_root_menu_items as $menu_item) {
				$page = $this->datastore->get_item_data($menu_item);
				$html_form .= '<li class="menu_item" id="'.$page->id.'" menu_id="'.$page->id.'"><i class="glyphicon glyphicon-move"></i> '.substr($page->title,0,10).'</li>';
			}

			$html_form .= '</ul></div></div>';
			$html_form .= '<div class="col-xs-12 col-sm-4">
			<h4>list of available pages</h4>
			<ul id="available_pages" class="menu_items_pages">';
			foreach ($oa_items as $value) {
				$html_form .= '<li class="menu_item" id="'.$value->id.'" menu_id="'.$value->id.'"><i class="glyphicon glyphicon-move"></i> '.substr($value->title,0,10).'</li>';
			}
			$html_form .= '</ul></div></div>';



			$html_form .= '<input type="text" id="menu_order_serialised" class="form-control" name="serialisation" value="'.$s_serialisation.'">';
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
			$flot = new Flot;

			$s_id = $flot->s_post_var("menu_id", false);

			if($s_id){
				$s_name = $flot->s_post_var("name", false);
				if($s_name !== false)
					$this->o_loaded_menu_object->title = urldecode($s_name);

				$s_serialisation = $flot->s_post_var("serialisation", false);
				if($s_serialisation !== false)
					$this->o_loaded_menu_object->serialisation = urldecode($s_serialisation);

				$this->datastore->_set_menu_data($this->o_loaded_menu_object);
				$this->datastore->_save_datastore("menus");
			}else{echo "no id";}
		}
	}
?>