<?php
	# manage site

	/*
	this page handles both gets and sets.
	ideally we could check for post vars, process set request, then location change to a get
	however we also need to show invalid post requests? maybe we could just do that client side.
	*/

	require_once('../core/flot.php');


	$flot = new Flot;
	$admin_ui = new AdminUI;


	if(!$flot->b_is_user_admin()){
		# forward them to login page
		$flot->_page_change("/flot_flot/admin/login.php");
	}


	$html_main_admin_content = "";

	if($flot->b_post_vars()){
		//print_r($_POST);
		# handle post request
		$s_section = $flot->s_post_var_from_allowed("section", array("items", "pictures", "menus", "settings"), "items");

		switch($s_section){
			case "items":
				# get the id, find the item, then try replacing the attributes
				$item_id = $flot->s_post_var("item_id", false);
				if($item_id){

					$o_item = $flot->datastore->get_item_data($item_id);

					if($o_item){
						$Item = new Item($o_item);

						$Item->update_from_post();

						# save the item
						$Item->save();

						# change location to view the item
					}
				}
				break;
		}



		# location change to corresponding get
	}else{
		$s_section = $flot->s_get_var_from_allowed("section", array("items", "pictures", "menus", "settings"), "items");



		switch($s_section){
			case "items":
				$s_action = $flot->s_get_var_from_allowed("action", array("edit", "list"), "list");

				switch ($s_action) {
					case 'edit':
						$s_page_id = $flot->s_get_var('item', false);

						if($s_page_id){
							# get the item
							$o_item = $flot->datastore->get_item_data($s_page_id);

							# get the oncology

							# render a form
							$Item = new Item($o_item);

							$html_main_admin_content = $Item->html_edit_form();
						}
						break;
					
					case 'list':
						# list all pages that can be edited (pagination ?)
						$oa_pages = $flot->oa_pages();
		         		$hmtl_pages_ui = "";

		         		if(count($oa_pages) > 0)
		         		{
		         			$hmtl_pages_ui .= '<ul class="list-group">';
			         		foreach ($oa_pages as $o_page) {
			         			# code...
			         			$hmtl_pages_ui .= '<li><a href="/flot_flot/admin/index.php?section=items&oncology=page&item='.$o_page->id.'&action=edit">';
			         			$hmtl_pages_ui .= $o_page->title;
			         			$hmtl_pages_ui .= '</a></li>';
			         		}
			         		$hmtl_pages_ui .= '</ul>';
			         	}else{
			         		$hmtl_pages_ui .= "no pages..";
			         	}

			         	$html_main_admin_content = $hmtl_pages_ui;
						break;
				}

	     		
				break;
			case "pictures":
				$html_pictures_ui = "pictures";
				$html_main_admin_content = $html_pictures_ui;
				break;
			case "menus":
				$html_menu_ui = "menus";
				$html_main_admin_content = $html_menu_ui;
				break;
			case "settings":
				$html_settings_ui = "settings";
				$html_main_admin_content = $html_settings_ui;
				break;
		}
	}

	#
	# if we're still here, render a page for the user
	#

	$admin_ui->html_make_admin_page($flot->s_admin_header(), $admin_ui->html_make_left_menu(), $html_main_admin_content);
?>