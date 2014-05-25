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

			$sa_menus = explode(';', urldecode($this->o_loaded_menu_object->serialisation));

			
			$s_seed_menu_items = $this->s_take_out_menu_items("root", $sa_menus);
			$html_menu .= $this->s_fill_out_menu($s_seed_menu_items, $sa_menus);

			return $html_menu;
		}
		function s_take_out_menu_items($s_parent, $sa_menus){
			$s_found = "";
			// returns a string of comma seperated menu items, or nothing if none found
			for ($c_pos = 0; $c_pos < count($sa_menus); $c_pos++){
				$sa_menu_parts = explode(":", $sa_menus[$c_pos]);
				if($sa_menu_parts[0] === $s_parent)
					$s_found .= $sa_menu_parts[1];
			}
			return $s_found;
		}
		function s_fill_out_menu($s_menu_items, $sa_menus){
			// make a list of menu items, and recurse through child items too
			$s_return = "";
			if($s_menu_items !== ""){

				$sa_menu_items = explode(",", $s_menu_items);
				$s_return .= "<ul>";
				foreach ($sa_menu_items as $s_menu_item) {

					$o_Item = new Item($this->datastore->get_item_data($s_menu_item));

					$s_return .= '<li><a href="'.$o_Item->o_loaded_item_object->url.'">'.$o_Item->o_loaded_item_object->title.'</a>';
					$s_return .= $this->s_fill_out_menu($this->s_take_out_menu_items($s_menu_item, $sa_menus), $sa_menus);
					$s_return .= "</li>";
				}
				$s_return .= "</ul>";
			}
			return $s_return;
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

			$html_form .= '<p>Drag pages from the list of available pages, to the menu on the left. Then drag to re-order, or click "submenu" to make a submenu, for that menu item.</p>';

			$html_form .= '<form id="menu_edit_form" role="form" method="post" action="index.php">';



			# title
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="menu_name">Name</label><input type="text" class="form-control" name="name" placeholder="menu name" value="'.$s_name.'">';
			$html_form .= '</div>';

			$html_form .= '<div class="row">';
			$html_form .= '<div class="col-xs-12 col-sm-8">
			<h4>re-order</h4><p id="menu_order_output"></p><div id="menu_order_area"><ul class="menu_items_pages">';

			foreach ($sa_root_menu_items as $menu_item) {
				$page = $this->datastore->get_item_data($menu_item);
				if($page){
					$html_form .= '<li class="menu_item clearer" menu_id="'.$page->id.'"><i class="glyphicon glyphicon-resize-vertical"></i> <span class="title">'.$page->title.'</span><a class="btn btn-sm btn-danger pull-right" href="javascript:delete_menu_item(\''.$page->id.'\')"><i class="glyphicon glyphicon-remove"></i> remove</a><a class="btn btn-sm btn-info pull-right" href="javascript:sub_menu(\''.$page->id.'\')"><i class="glyphicon glyphicon-arrow-right"></i> submenu</a></li>';
				}else{
					$html_form .= "";
				}
			}

			$html_form .= '</ul></div></div>';
			$html_form .= '<div class="col-xs-12 col-sm-4">
			<h4>list of available pages</h4>
			<ul id="available_pages" class="menu_items_pages">';
			foreach ($oa_items as $value) {
				$html_form .= '<li class="menu_item clearer" menu_id="'.$value->id.'"><i class="glyphicon glyphicon-move"></i> <span class="title" alt="'.$value->title.'">'.substr($value->title,0,10).'</span></li>';
			}
			$html_form .= '</ul></div></div>';


			$html_form .= '<script>';
			foreach ($oa_items as $value) {
				$html_form .= 'sa_item_name_look_up["'.$value->id.'"] = "'.$value->title.'";';
			}
			$html_form .= '</script>';



			$html_form .= '<input type="hidden" id="menu_order_serialised" class="form-control" name="serialisation" value="'.$s_serialisation.'">';
			$html_form .= '<input type="hidden" name="section" value="menus">';

			$html_form .= '<input type="hidden" name="menu_id" value="'.$s_id.'">';

			# save
			$html_form .= '<br/><div class="form-group">';

			$html_form .= '<input value="save" type="submit" class="form-control btn btn-success">';
			$html_form .= '</div>';

			$html_form .= '</form>';


			$html_form .= '<p>copy and paste the below code into your theme to use this menu</p>';
			$html_form .= '<kbd>{{menu:'.$s_name.'}}</kbd>';


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
				$this->datastore->b_save_datastore("menus");

				// regenerate pages
				$flot->_render_all_pages();
			}else{echo "no id";}
		}
	}
?>