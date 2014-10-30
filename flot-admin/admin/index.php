<?php
	# manage site

	/*
	this page handles both gets and sets.
	ideally we could check for post vars, process set request, then location change to a get
	however we also need to show invalid post requests? maybe we could just do that client side.
	*/

	require_once('../../flot-admin/core/base.php');
	require_once('../../flot-admin/core/flot.php');


	$flot = new Flot;
	$admin_ui = new AdminUI;


	if(!$flot->b_is_user_admin()){
		# forward them to login page
		$flot->_page_change("/flot-admin/admin/login.php");
	}


	$html_main_admin_content = "";
	$html_main_admin_content_menu = "";
	$s_body_class = "";

	$s_section = "";

	$ufUf = new UtilityFunctions;
		
	if($ufUf->b_post_vars()){
		#
		# handle post request
		#
		$s_action = $ufUf->s_post_var_from_allowed("action", array("edit"), "edit");		
		$s_section = $ufUf->s_post_var_from_allowed("section", array("items", "elements", "pictures", "menus", "settings", "oncologies"), "items");

		switch($s_section){
			case "items":
				switch ($s_action) {
					case 'edit':
						# get the id, find the item, then try replacing the attributes
						$item_id = $ufUf->s_post_var("item_id", false);
						if($item_id){
							// we have an item id, now we'll try and get the corresponding item information
							$o_item = $flot->datastore->get_item_data($item_id);
							
							$o_full_item = $flot->datastore->o_get_full_item($item_id);


							if($o_item && isset($o_full_item)){
								$Item = new Item($o_item);

								$Item->_set_full_item($o_full_item);
								$Item->update_from_post();

								$s_preview = $ufUf->s_post_var_from_allowed("preview", array("true", "false"), "false");

								if($s_preview === "true"){						
									echo $Item->html_page;
									//ob_clean();
									$Item->render();
									exit();
								}else{
									// save datastore
									$Item->persist_after_update_from_post();

									# persist (or not) the item
									// save does a render and update wbepage render
									$Item->save();
									
									# change location to view the item
									$flot->_page_change("/flot-admin/admin/index.php?section=items&oncology=page&action=list");
								}
							}else{
								echo "no loaded item & full item";
							}
						}
						break;
				}
				break;
			case "elements":
				switch ($s_action) {
					case 'edit':
						# get the id, find the item, then try replacing the attributes
						$element_id = $ufUf->s_post_var("element_id", false);
						if($element_id){
							// we have an item id, now we'll try and get the corresponding item information
							$o_element = $flot->datastore->get_element_data($element_id);
							
							$o_full_element = $flot->datastore->o_get_full_element($element_id);


							if($o_element && isset($o_full_element)){
								$Element = new Element($o_element);

								$Element->_set_full_element($o_full_element);
								$Element->update_from_post();
								
								# change location to view the item
								$flot->_page_change("/flot-admin/admin/index.php?section=elements&action=list");
							}else{
								echo "no loaded element & full element";
							}
						}
						break;
				}
				break;
			case "oncologies":
				switch ($s_action) {
					case 'edit':
						# get the id, find the item, then try replacing the attributes
						$s_oncology_id = $ufUf->s_post_var("oncology_id", false);
						if($s_oncology_id){
							// we have an item id, now we'll try and get the corresponding item information
							$json_oncology = $flot->datastore->get_oncology_data($s_oncology_id);
							

							if($json_oncology){
								$Oncology = new Oncology($json_oncology);

								$Oncology->update_from_post();

								# change location to view the item
								$flot->_page_change("/flot-admin/admin/index.php?section=oncologies&action=list");
							}else{
								echo "no loaded page type";
							}
						}
						break;
				}
				break;
			case "menus":
				switch ($s_action) {
					case 'edit':
						# get the id, find the item, then try replacing the attributes
						$menu_id = $ufUf->s_post_var("menu_id", false);
						if($menu_id){

							$o_menu = $flot->datastore->get_menu_data($menu_id);

							if($o_menu){
								$Menu = new Menu($o_menu);

								$Menu->update_from_post();

								# persist (or not) the item
								$Menu->save();


								# change location to view the item
								$flot->_page_change("/flot-admin/admin/index.php?section=menus&action=list");
							}
						}
						break;
				}
				break;
			case "settings":
				switch ($s_action) {
					case 'edit':		
						foreach ($_POST as $param_name => $param_val) {
							if(isset($flot->datastore->settings->{$param_name})){
								// the posted variable already exists, so we'll update it
								$s_old_theme = $flot->datastore->settings->theme;
								$flot->datastore->settings->{$param_name} = $param_val;
								$flot->datastore->b_save_datastore("settings");

								$s_new_theme = $flot->datastore->settings->theme;

								if($s_old_theme !== $s_new_theme){
									$flot->_theme_changed();
								}
							}
						}

						# change location to view the item
						$flot->_page_change("/flot-admin/admin/index.php?section=settings");
						break;
				}
				break;
			default:
				// keep alive - keep user logged in
				// js posts here and sessions is checked keeping them logged in
				break;
		}

		# location change to corresponding get
	}else{
		#
		# no post vars, this is a GET request ?
		#

		$s_section = $ufUf->s_get_var_from_allowed("section", array("items", "pictures", "menus", "settings", "errors", "requirements", "oncologies", "flot", "elements"), "items");

		switch($s_section){
			case "items":
				$s_action = $ufUf->s_get_var_from_allowed("action", array("edit", "list", "new", "delete"), "list");

				switch ($s_action) {
					case 'edit':
						$s_page_id = $ufUf->s_get_var('item', false);
						# menu items; purge from cache, preview, regenerate, delete
						
						if($s_page_id){
							# get the item
							$o_item = $flot->datastore->get_item_data($s_page_id);

							$o_full_item = $flot->datastore->o_get_full_item($s_page_id);

							# get the oncology

							# render a form
							$Item = new Item($o_item);
							$Item->_set_full_item($o_full_item);

							$html_main_admin_content .= $Item->html_edit_form();

							// make left menu smaller, to give more focus to editing
							$s_body_class = "smaller_left";
						}
						break;
					
					case 'list':
						# list all pages that can be edited (pagination ?)
						$odOD = new OncologyData;
						$s_oncology_filter = $ufUf->s_get_var('oncology', false);
						$oa_pages = $flot->oa_pages();


						if($s_oncology_filter !== false){
							// filter pages retrieved to be of the right page type
							$oa_filtered_pages = array();
							foreach ($oa_pages as $page) {
								$s_oncology_id = urldecode($page->oncology);
								if($s_oncology_id === $s_oncology_filter){
									array_push($oa_filtered_pages, $page);
								}
							}
							$oa_pages = $oa_filtered_pages;
						}

		         		$hmtl_pages_ui = "";
						$hmtl_pages_ui .= ''.$admin_ui->html_make_page_add_button().'<div class="btn-group"><a class="btn btn-default btn-sm" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=flot&action=regenerate"><i class="glyphicon glyphicon-refresh"></i> regenerate all pages</a></div><hr/>';

		         		if(count($oa_pages) > 0)
		         		{
		         			$hmtl_pages_ui .= '<table id="admin_table_list" class="table table-hover"><thead><tr><th>Edit page&nbsp;<i class="glyphicon glyphicon-edit"></i></th><th>View page&nbsp;<i class="glyphicon glyphicon-new-window"></i></th><th class="hidden-xs hidden-sm">page type</th><th class="hidden-xs hidden-sm">last changed</th><th class="hidden-xs hidden-sm">author</th><th>published</th><th><a class="btn btn-danger btn-xs item_delete_start"><i class="glyphicon glyphicon-trash"></i><span class="hidden-xs">&nbsp;Delete</span></a><a class="btn btn-success btn-xs item_delete_done"><i class="glyphicon glyphicon-ok"></i><span class="hidden-xs">&nbsp;Done</span></a></th></tr></thead><tbody>';

		         			
			         		foreach ($oa_pages as $o_page) {
			         			//
			         			// get data
			         			//
								$s_id = urldecode($o_page->id);
								$s_title = urldecode($o_page->title);
								$s_oncology = urldecode($o_page->oncology);
								$s_url = urldecode($o_page->url);
								$s_author = urldecode($o_page->author);
								$s_date_modified = urldecode($o_page->date_modified);
								$s_published = (urldecode($o_page->published) === "true" ? '<i class="green glyphicon glyphicon-ok"></i>' : '<i class="red glyphicon glyphicon-remove"></i>');

								//
								// sanitise data if necessary
								//
								if($s_date_modified !== ""){
									$s_date_modified = explode('-', $s_date_modified);
									$s_date_modified = date("D jS M Y", mktime(0, 0, 0, $s_date_modified[1], $s_date_modified[0], $s_date_modified[2]));
								}


								$s_url_text = $s_url;

								$oUrlStuff = new UrlStuff;
								$s_url = $oUrlStuff->s_format_url_from_item_url($s_url);

								if($s_url === "/"){
									// homepage
									$s_url_text = ' <i class="glyphicon glyphicon-home"></i> Homepage';
								}

								$s_link_class = '';
								if(urldecode($o_page->published) !== "true"){
									$s_link_class = ' style="display:none;"';
								}


			         			# code...
			         			$hmtl_pages_ui .= '<tr><td><a class="btn btn-view btn-xs" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=items&item='.$s_id.'&action=edit">';
			         			$hmtl_pages_ui .= $s_title;
			         			$s_url_link = '<a target="_blank" href="'.$s_url.'" '.$s_link_class.' class="view_link">'.$s_url_text.'</a>';
			         			if(urldecode($o_page->published) === "false"){
			         				$s_url_link = '<span class="gray"><i class="glyphicon glyphicon-eye-close"></i> unpublished</span>';
			         			}

			         			$hmtl_pages_ui .= '</a></td><td>'.$s_url_link.'</td><td class="hidden-xs hidden-sm">'.$odOD->s_oncology_name_from_id($s_oncology).'</td><td class="hidden-xs hidden-sm">'.$s_date_modified.'</td><td class="hidden-xs hidden-sm">'.$s_author.'</td><td>'.$s_published.'</td><td><a href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=items&oncology=page&item='.$o_page->id.'&action=delete" class="btn btn-danger btn-xs item_delete"><i class="glyphicon glyphicon-trash"></i><span class="hidden-xs">&nbsp;delete</span></a></td></tr>';
			         		}
			         		$hmtl_pages_ui .= '</tbody></table>';
			         	}else{
			         		$hmtl_pages_ui .= "no pages..";
			         	}

			         	$html_main_admin_content = $hmtl_pages_ui;
						break;
					
					case 'new':
						# create the new item, then do a page change to be editing it

						$s_oncology = $ufUf->s_get_var("oncology", false);

						if($s_oncology){
							$s_newitem_id = $flot->datastore->s_new_item($s_oncology);

							$s_new_page = S_BASE_EXTENSION."flot-admin/admin/index.php?section=items&oncology=$s_oncology&item=".$s_newitem_id."&action=edit";
							$flot->_page_change($s_new_page);
						}else{
							echo "no page type :(";
						}
						break;
					
					case 'delete':
						# create the new item, then do a page change to be editing it
						$s_page_id = $ufUf->s_get_var('item', false);
						if($s_page_id){
							// delete 'physical' copy on disk
							$o_item = $flot->datastore->get_item_data($s_page_id);
							$Item = new Item($o_item);
							$Item->delete();
							// remove from datastore
							$flot->datastore->_delete_item($s_page_id);

							$s_new_page = S_BASE_EXTENSION."flot-admin/admin/index.php?section=items&oncology=page&action=list";
							$flot->_page_change($s_new_page);
						}
						break;
				}
				break;

			

			case "elements":
				$s_action = $ufUf->s_get_var_from_allowed("action", array("edit", "list", "new", "delete"), "list");

				switch ($s_action) {
					
					case 'edit':
						$s_element_id = $ufUf->s_get_var('element', false);
						# menu items; purge from cache, preview, regenerate, delete
						
						if($s_element_id){
							# get the item
							$o_element = $flot->datastore->get_element_data($s_element_id);

							$o_full_element = $flot->datastore->o_get_full_element($s_element_id);

							# render a form
							$Element = new Element($o_element);
							$Element->_set_full_element($o_full_element);

							$html_main_admin_content .= $Element->html_edit_form();

							// make left menu smaller, to give more focus to editing
							$s_body_class = "smaller_left";
						}
						break;
					
					case 'list':
						# list all elements 		
						$oa_elements = $flot->oa_elements();


		         		$hmtl_pages_ui = "";
						$hmtl_pages_ui .= '<div class="btn-group edit_item_general_toolbar"><a class="btn btn-default btn-sm" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=elements&action=new"><i class="glyphicon glyphicon-plus"></i> add a new element</a></div><hr/>';

		         		if(count($oa_elements) > 0)
		         		{
		         			$hmtl_pages_ui .= '<table id="admin_table_list" class="table table-hover"><thead><tr><th>Edit element&nbsp;<i class="glyphicon glyphicon-edit"></i></th><th class="hidden-xs hidden-sm">last changed</th><th class="hidden-xs hidden-sm">author</th><th>published</th><th><a class="btn btn-danger btn-xs item_delete_start"><i class="glyphicon glyphicon-trash"></i><span class="hidden-xs">&nbsp;Delete</span></a><a class="btn btn-success btn-xs item_delete_done"><i class="glyphicon glyphicon-ok"></i><span class="hidden-xs">&nbsp;Done</span></a></th></tr></thead><tbody>';

		         			
			         		foreach ($oa_elements as $o_element) {
			         			//
			         			// get data
			         			//
								$s_id = urldecode($o_element->id);
								$s_title = urldecode($o_element->title);
								
								$s_author = urldecode($o_element->author);
								$s_date_modified = urldecode($o_element->date_modified);
								$s_published = (urldecode($o_element->published) === "true" ? '<i class="green glyphicon glyphicon-ok"></i>' : '<i class="red glyphicon glyphicon-remove"></i>');

								//
								// sanitise data if necessary
								//
								if($s_date_modified !== ""){
									$s_date_modified = explode('-', $s_date_modified);
									$s_date_modified = date("D jS M Y", mktime(0, 0, 0, $s_date_modified[1], $s_date_modified[0], $s_date_modified[2]));
								}

								$s_link_class = '';
								if(urldecode($o_element->published) !== "true"){
									$s_link_class = ' style="display:none;"';
								}

			         			$hmtl_pages_ui .= '<tr><td><a class="btn btn-view btn-xs" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=elements&element='.$s_id.'&action=edit">';
			         			$hmtl_pages_ui .= $s_title;
			         			
			         			
			         			$hmtl_pages_ui .= '</a></td><td class="hidden-xs hidden-sm">'.$s_date_modified.'</td><td class="hidden-xs hidden-sm">'.$s_author.'</td><td>'.$s_published.'</td><td><a href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=elements&element='.$s_id.'&action=delete" class="btn btn-danger btn-xs item_delete"><i class="glyphicon glyphicon-trash"></i><span class="hidden-xs">&nbsp;delete</span></a></td></tr>';
			         		}
			         		$hmtl_pages_ui .= '</tbody></table>';
			         	}else{
			         		$hmtl_pages_ui .= "no elements..";
			         	}

			         	$html_main_admin_content = $hmtl_pages_ui;
			         	
						break;
					
					case 'new':

						$s_newelement_id = $flot->datastore->s_new_element();

						$s_new_page = S_BASE_EXTENSION."flot-admin/admin/index.php?section=elements&element=".$s_newelement_id."&action=edit";
						$flot->_page_change($s_new_page);
						
						break;
					

					
					case 'delete':
						# create the new item, then do a page change to be editing it
						$s_element_id = $ufUf->s_get_var('element', false);
						if($s_element_id){
							// delete 'physical' copy on disk
							$o_element = $flot->datastore->get_element_data($s_element_id);
							// remove from datastore
							$flot->datastore->_delete_element($s_element_id);

							$s_new_page = S_BASE_EXTENSION."flot-admin/admin/index.php?section=elements&action=list";
							$flot->_page_change($s_new_page);
						}
						break;
				}
				break;

			

			case "menus":
				$s_action = $ufUf->s_get_var_from_allowed("action", array("edit", "list", "new", "delete"), "list");

				switch ($s_action) {
					case 'edit':
						$s_menu_id = $ufUf->s_get_var('menu', false);
						# menu items; purge from cache, preview, regenerate, delete
						
						if($s_menu_id){
							# get the item
							$o_menu = $flot->datastore->get_menu_data($s_menu_id);

							# get the oncology

							# render a form
							$Menu = new Menu($o_menu);

							$html_main_admin_content .= $Menu->html_edit_form();

							// make left menu smaller, to give more focus to editing
							$s_body_class = "smaller_left";
						}else{
							$html_main_admin_content .= "flot couln't find that menu :(";
						}
						break;					
					case 'list':
						# list all pages that can be edited (pagination ?)
						$oa_menus = $flot->oa_menus();
		         		$hmtl_menus_ui = "";
						$hmtl_menus_ui .= '<a class="btn btn-default btn-sm" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=menus&action=new"><i class="glyphicon glyphicon-plus"></i> add a new menu</a><hr/>';

		         		if(count($oa_menus) > 0)
		         		{
		         			$hmtl_menus_ui .= '<table id="admin_table_list" class="table table-hover"><thead><tr><th>menu name</th><th>delete</th></tr></thead><tbody>';
			         		foreach ($oa_menus as $o_menu) {
								$s_id = urldecode($o_menu->id);
								$s_title = urldecode($o_menu->title);

			         			# code...
			         			$hmtl_menus_ui .= '<tr><td><a class="btn btn-view" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=menus&menu='.$s_id.'&action=edit">';
			         			$hmtl_menus_ui .= $s_title;
			         			$hmtl_menus_ui .= '</a></td>';

								$hmtl_menus_ui .= '<td><a href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=menus&menu='.$o_menu->id.'&action=delete" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> delete</a></td></tr>';
			         		}
			         		$hmtl_menus_ui .= '</tbody></table>';
			         	}else{
			         		$hmtl_menus_ui .= "no menus..";
			         	}

			         	$html_main_admin_content = $hmtl_menus_ui;
						break;
					
					case 'new':
						
						# create the new item, then do a page change to be editing it

						$s_new_menu_id = $flot->datastore->s_new_menu();


						$s_new_menu = S_BASE_EXTENSION."flot-admin/admin/index.php?section=menus&menu=".$s_new_menu_id."&action=edit";
						$flot->_page_change($s_new_menu);
						
						break;
					
					case 'delete':
						
						# create the new item, then do a page change to be editing it
						$s_menu_id = $ufUf->s_get_var('menu', false);
						if($s_menu_id){
							$flot->datastore->_delete_menu($s_menu_id);

							$s_new_page = S_BASE_EXTENSION."flot-admin/admin/index.php?section=menus&action=list";
							$flot->_page_change($s_new_page);
						}
						
						break;
				}

	     		
				break;
			case "pictures":
				$s_action = $ufUf->s_get_var_from_allowed("action", array("select", "browse"), "browse");

				#
				# top menu
				#


				$o_FileBrowser = new FileBrowser($s_action);

				$html_main_admin_content .= $o_FileBrowser->html_make_browser();

				if($s_action === "select"){
					echo $admin_ui->html_admin_headers_base();
					echo $admin_ui->html_admin_headers_pictures();
					echo $html_main_admin_content;
					exit();
				}

				break;
			case "settings":
				$html_main_admin_content = $admin_ui->html_make_settings_form($flot->datastore->settings);
				break;
			case "oncologies":
				$s_action = $ufUf->s_get_var_from_allowed("action", array("edit", "list", "new", "delete"), "list");

				switch ($s_action) {
					case 'edit':
					
						$s_oncology_id = $ufUf->s_get_var('id', false);
						# menu items; purge from cache, preview, regenerate, delete
						
						if($s_oncology_id){
							# get the oncology data
							$json_oncology = $flot->datastore->get_oncology_data($s_oncology_id);

							# render a form
							$Oncology = new oncology($json_oncology);
							
							$html_main_admin_content .= $Oncology->html_edit_form();

							// make left menu smaller, to give more focus to editing
							$s_body_class = "smaller_left";
						}
						
						break;
					
					case 'list':
					
						# list all pages that can be edited (pagination ?)
						$oa_oncologies = $flot->oa_oncologies();
		         		$hmtl_pages_ui = "";
						$hmtl_pages_ui .= '<a class="btn btn-default btn-sm" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=oncologies&action=new"><i class="glyphicon glyphicon-plus"></i> add a new page type</a><hr/>';

						$hmtl_pages_ui .= '<div class="alert alert-info">Page types are individually defined templates to make webpages from.</div>';

		         		if(count($oa_oncologies) > 0)
		         		{
		         			$hmtl_pages_ui .= '<table id="admin_table_list" class="table table-hover"><thead><tr><th>Edit page type&nbsp;<i class="glyphicon glyphicon-edit"></i></th><th>#Instances</th><th><a class="btn btn-danger btn-xs item_delete_start"><i class="glyphicon glyphicon-trash"></i>&nbsp;Delete</a><a class="btn btn-success btn-xs item_delete_done"><i class="glyphicon glyphicon-ok"></i>&nbsp;Done</a></th></tr></thead><tbody>';
			         		foreach ($oa_oncologies as $o_oncology) {
			         			//
			         			// get data
			         			//
								$s_id = urldecode($o_oncology->id);
								$s_title = urldecode($o_oncology->title);
								$s_editable = urldecode($o_oncology->editable);


			         			$s_link = $s_title;
			         			$s_deletable_link = '';

			         			$s_disabled = ' disabled';

			         			if($s_editable === "true"){
			         				$s_disabled = '';

			         				$s_deletable_link = '<a href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=oncologies&id='.$s_id.'&action=delete" class="btn btn-danger btn-xs item_delete"><i class="glyphicon glyphicon-trash"></i> delete</a>';
			         			}

		         				$s_link = '<a class="btn btn-view'.$s_disabled.'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=oncologies&id='.$s_id.'&action=edit">'.$s_link.'</a>';

			         			# code...
			         			$hmtl_pages_ui .= '<tr><td>';
			         			$hmtl_pages_ui .= $s_link;


			         			$hmtl_pages_ui .= '</td><td>?</td><td>'.$s_deletable_link.'</td></tr>';
			         		}
			         		$hmtl_pages_ui .= '</tbody></table>';
			         	}else{
			         		$hmtl_pages_ui .= "no page types yet..";
			         	}

			         	$html_main_admin_content = $hmtl_pages_ui;
						break;
					
					case 'new':
					
						# create the new item, then do a page change to be editing it

						$s_newitem_id = $flot->datastore->s_new_oncology();

						$s_new_page = "/flot-admin/admin/index.php?section=oncologies&id=".$s_newitem_id."&action=edit";	
						$flot->_page_change($s_new_page);
						
						break;
					
					case 'delete':
					
						$s_oncology_id = $ufUf->s_get_var('id', false);
						
						if($s_oncology_id){

							// remove from datastore
							$flot->datastore->_delete_oncology($s_oncology_id);

							$s_new_page = "/flot-admin/admin/index.php?section=oncologies&action=list";
							$flot->_page_change($s_new_page);
						}
						break;
				}
				break;
			case "errors":
				$s_action = $ufUf->s_get_var_from_allowed("action", array("view", "clear"), "view");

				switch($s_action){
					case "clear":
						// clear log
						$fu_FileUtil = new FileUtilities;
						$fu_FileUtil->_wipe_errors();

						// reload to view						
						$flot->_page_change("/flot-admin/admin/index.php?section=errors&action=view");

						break;
					case "view":
						$html_main_admin_content = '<a class="btn btn-default btn-sm" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=errors&action=clear"><i class="glyphicon glyphicon-trash"></i> clear/delete log</a><hr/>';
						$html_main_admin_content .= $admin_ui->html_make_error_page();
						break;
				}
				break;
			case "requirements":
				$s_action = $ufUf->s_get_var_from_allowed("action", array("view"), "view");

				switch($s_action){
					case "view":
						$html_main_admin_content .= $admin_ui->html_requirements_list();
						break;
				}
				break;
			case "flot":
				$s_action = $ufUf->s_get_var_from_allowed("action", array("regenerate", "list_pages"), false);

				switch($s_action){
					case "regenerate":
						$flot->_render_all_pages();
						
						// back to same page
						$s_new_page = "/flot-admin/admin/index.php?section=items&message=".urlencode("Flot has regenerated all pages");
						$flot->_page_change($s_new_page);
						break;

					case "list_pages":
						// return json of all pages
						header('Content-Type: application/json');
						echo json_encode($flot->oa_pages());
						exit();
						break;
				}
				break;
		}
	}

	#
	# if we're still here, render a page for the user
	#

	$admin_ui->html_make_admin_page($admin_ui->s_admin_header($s_section), $admin_ui->html_make_left_menu($s_section), $html_main_admin_content, $html_main_admin_content_menu, $s_body_class);
?>