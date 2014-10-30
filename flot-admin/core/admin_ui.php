<?php

	class AdminUI {

		function __construct() {
		}
		function html_make_left_menu($s_active_section){
			$html_left_menu = '';

			$fu_FileUtil = new FileUtilities;
			$fr_FlotRequirements = new FlotRequirements;
			$dD = new DataStore;

			$oa_oncologies = $dD->oncologies;
			
			$html_left_menu .= '<div id="admin_menu_left">
					<a class="items admin_menu_left'.$this->s_active_or_empty("items", $s_active_section).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=items"><i class="glyphicon glyphicon-folder-open"></i><span class="small-hidden condensed_hidden"> Contents</span></a>';

			// make a submenu item for each page type
			if(count($oa_oncologies) > 0){
				foreach ($oa_oncologies as $o_oncology) {
					$s_id = urldecode($o_oncology->id);
					$s_title = urldecode($o_oncology->title);

					$html_left_menu .= '<a class="admin_menu_left page_type'.$this->s_active_oncology($s_id).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=items&oncology='.$s_id.'"><i class="glyphicon glyphicon-file"></i><span class="small-hidden condensed_hidden"> '.$s_title.'</span></a>';
				}
			}


			$html_left_menu .= '<a class="elements admin_menu_left'.$this->s_active_or_empty("elements", $s_active_section).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=elements"><i class="glyphicon glyphicon-paperclip"></i><span class="small-hidden condensed_hidden"> Elements</span></a>
			<a class="pictures admin_menu_left'.$this->s_active_or_empty("pictures", $s_active_section).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=pictures"><i class="glyphicon glyphicon-picture"></i><span class="small-hidden condensed_hidden"> Pictures</span></a>
					<a class="menus admin_menu_left'.$this->s_active_or_empty("menus", $s_active_section).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=menus"><i class="glyphicon glyphicon-list"></i><span class="small-hidden condensed_hidden"> Menus</span></a>
					<a class="oncologies admin_menu_left'.$this->s_active_or_empty("oncologies", $s_active_section).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=oncologies"><i class="glyphicon glyphicon-list-alt"></i><span class="small-hidden condensed_hidden"> Page types</span></a>
					<a class="settings admin_menu_left'.$this->s_active_or_empty("settings", $s_active_section).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=settings"><i class="glyphicon glyphicon-cog"></i><span class="small-hidden condensed_hidden"> Settings</span></a>';
			if($fu_FileUtil->b_errors()){
				$html_left_menu .= '<a class="errors admin_menu_left'.$this->s_active_or_empty("errors", $s_active_section).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=errors"><i class="glyphicon glyphicon-fire"></i><span class="small-hidden condensed_hidden"> Errors</span></a>';
			}
			if(!$fr_FlotRequirements->b_ongoing_requirements_met()){
				$html_left_menu .= '<a class="requirements admin_menu_left'.$this->s_active_or_empty("requirements", $s_active_section).'" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=requirements"><i class="glyphicon glyphicon-exclamation-sign"></i><span class="small-hidden condensed_hidden"> Requirements</span></a>';
			}
			$html_left_menu .= '</div>';

			return $html_left_menu;
		}
		function html_requirements_list(){			
			$fr_FlotRequirements = new FlotRequirements;

			$fr_FlotRequirements->b_ongoing_requirements_met();

			$sa_reqs = $fr_FlotRequirements->sa_requirements_to_remedy();

			if(empty($sa_reqs)){
				return '<div class="alert alert-success">no problems</div>';
			}
			$html_return = '<a class="btn btn-default btn-sm" href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=requirements"><i class="glyphicon glyphicon-refresh"></i> re-check</a><hr/>';
			foreach ($sa_reqs as $s_problem) {
				$html_return .= '<div class="alert alert-danger">'.$s_problem.'</div>';
			}
			return $html_return;
		}
		function html_make_admin_page($html_header, $html_left_menu, $html_make_admin_content, $html_make_admin_content_menu, $s_body_class){

			$html_add_content_button = '';

			$ufUF = new UtilityFunctions;

			$html_message_alert = $ufUF->s_get_var('message', '');
			if($html_message_alert !== ''){
				$html_message_alert = '<div class="alert alert-info alert-dismissible"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'.$html_message_alert.'</div>';
			}

			include(S_BASE_PATH.'flot-admin/admin/ui/template.php');
			exit();
		}
		function s_active_or_empty($s_me, $s_current){
			if($s_me === $s_current)
				return " active";
			else
				return "";
		}
		function s_active_oncology($s_me){
			$ufUF = new UtilityFunctions;
			$s_oncology = $ufUF->s_get_var('oncology', '');

			if($s_me === $s_oncology)
				return " active";
			else
				return "";
		}
		function s_admin_header($s_section = ""){
			$s_header = "";

			$s_header .= $this->html_admin_headers_base();


			switch ($s_section){
				case "items":
					$ufUf = new UtilityFunctions;
					$s_action = $ufUf->s_get_var_from_allowed("action", array("edit", "list"), "list");

					switch($s_action){
						case "edit":
							# ckeditor
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/external_integrations/ckeditor/ckeditor.js"></script>';

							# text angular
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/external_integrations/text_angular/textAngular-sanitize.min.js"></script>';
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/external_integrations/text_angular/textAngular.min.js"></script>';

							# general admin js
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin_itemedit.js"></script>';

							$s_header .= $this->html_admin_headers_pictures();
							break;
						case "list":
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin_item_list.js"></script>';
							break;
					}
					break;
				case "elements":
					$ufUf = new UtilityFunctions;
					$s_action = $ufUf->s_get_var_from_allowed("action", array("edit", "list"), "list");

					switch($s_action){
						case "edit":
							# ckeditor

							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/external_integrations/ckeditor/ckeditor.js"></script>';
							
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin_itemedit.js"></script>';

							$s_header .= $this->html_admin_headers_pictures();
							break;
						case "list":
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin_item_list.js"></script>';
							break;
					}
					break;
				case "oncologies":
					$ufUf = new UtilityFunctions;
					$s_action = $ufUf->s_get_var_from_allowed("action", array("edit", "list"), "list");

					switch($s_action){
						case "list":
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin_item_list.js"></script>';
							break;
						case "edit":
							$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin_oncology_edit.js"></script>';
							break;
					}
					break;
				case "menus":
					# jquery ui, for sortables
					$s_header .= '<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>';
					// admin js for menus
					$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin_menus.js"></script>';
					break;
				case "pictures":
					# general admin js
					$s_header .= $this->html_admin_headers_pictures();
					break;

			}

			$s_header .= '<title>flot - manage your site</title>';

			return $s_header;
		}
		function html_admin_headers_pictures(){
			$s_header = "";
			$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/jquery.ui.widget.js"></script>';
			$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/jquery.iframe-transport.js"></script>';
			$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/jquery.fileupload.js"></script>';
			$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin_pictures.php"></script>';
			return $s_header;
		}
		function html_admin_headers_base(){
			$s_header = "";
			# bootstrap css
			$s_header .= '<link rel="stylesheet" href="'.S_BASE_EXTENSION.'flot-admin/admin/css/bootstrap.min.css">';
			# admin css
			$s_header .= '<link rel="stylesheet" href="'.S_BASE_EXTENSION.'flot-admin/admin/css/admin_style.css">';
			# google font
			$s_header .= "<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>";
			# font awesome
			$s_header .= '<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">';
    

			# jquery js
			$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/jquery.min.js"></script>';
			# bootstrap js
			$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/bootstrap.min.js"></script>';
			# default flot admin js
			$s_header .= '<script src="'.S_BASE_EXTENSION.'flot-admin/admin/js/admin.php"></script>';

			return $s_header;
		}
		function html_make_settings_form($jo_settings){
			$html_form = "";
			$suSU = new SettingsUtilities;

			$html_form .= '<h4>Settings</h4>';
			$html_form .= '<div class="alert alert-info">Only some settings can be edited at the moment, if you feel confident you can edit theme directly in the settings datastore.</div><hr/>';


			$html_form .= '<form role="form" method="post" action="index.php">';

			#
			# make tabs
			#

			# tab menu
			$html_form .= '<ul class="nav nav-tabs">';
			$html_form .= '<li class="active"><a href="#general" data-toggle="tab">General</a></li>';
			$html_form .= '<li><a href="#images" data-toggle="tab">Images</a></li>';    
			$html_form .= '<li><a href="#flot" data-toggle="tab">Flot</a></li>';    
			$html_form .= '</ul>';


			# tabs
			$html_form .= '<div class="tab-content">';

			# 
			# general tab
			#
			$html_form .= '<div class="tab-pane active" id="general">';

			# upload dir
			$html_form .= '<div class="form-group"><label for="setting_upload_dir">Upload folder (file path, relative from root)</label><input type="text" class="form-control input-sm" id="setting_upload_dir" placeholder="relative upload directory" disabled value="'.$jo_settings->upload_dir.'"></div>';

			# website name
			$html_form .= '<div class="form-group"><label for="setting_website_name">Website name</label><input type="text" name="site_name" class="form-control input-sm" id="setting_website_name" placeholder="website name" value="'.$jo_settings->site_name.'"></div>';


			# template
			$html_form .= '<div class="form-group input-group-sm">';
			$html_form .= '<label for="setting_theme_name">Theme</label>';

			$html_form .= '<select name="theme" class="form-control" id="settings_theme">';

			$file_utility = new FileBrowser;

			$sa_dirs = $file_utility->sa_themes_available();
			foreach ($sa_dirs as $s_theme_dir) {
				$s_selected = '';

				if($s_theme_dir === $jo_settings->theme){
					$s_selected = 'selected ';
				}
				$html_form .= '<option '.$s_selected.'value="'.$s_theme_dir.'" >'.$s_theme_dir.'</option>';
				
			}
			$html_form .= '</select>';
			$html_form .= '</div>';


			$html_form .= '</div>';
			# 
			# images tab
			#
			$html_form .= '<div class="tab-pane" id="images">';

			# thumbs

			$html_form .= '<h5>Thumbnail sizes</h5>';

			
			foreach ($jo_settings->thumb_sizes as $o_thumb_size) {
				
				$html_form .= '<div class="row form-group"><div class="col-xs-12"><label>'.$o_thumb_size->name.'</label><input type="text" class="form-control" placeholder="" value="'.$o_thumb_size->name.'" disabled></div></div><div class="row form-group"><div class="col-xs-12 col-sm-6"><label>max width (blank for none)</label><input type="text" class="form-control" placeholder="" value="'.$o_thumb_size->max_width.'" disabled></div><div class="col-xs-12 col-sm-6"><label>max height (blank for none)</label><input type="text" class="form-control" placeholder="" value="'.$o_thumb_size->max_height.'" disabled></div></div>';
			}

			$html_form .= '</div>';

			# 
			# flot tab
			#
			$html_form .= '<div class="tab-pane" id="flot">';
			$html_form .= '<table class="table table-condensed"><thead><tr><th>Property</th><th>Value</th></tr></thead><tbody>';
			
			// flot version
			$html_form .= '<tr><td>flot version</td><td><span class="label label-primary">'.$suSU->s_literal_flot_version().'</span></td></tr>';
			
			// max execution time
			$html_form .= '<tr><td>PHP max execution time (seconds)</td><td>'.$suSU->i_max_execution_time().'</td></tr>';
			
			// max input post time
			$html_form .= '<tr><td>PHP max input time (seconds)</td><td>'.$suSU->i_max_input_time().'</td></tr>';
			
			// post_max_size
			$html_form .= '<tr><td>PHP post max size</td><td>'.$suSU->i_post_max_size().'</td></tr>';
			
			// upload_max_filesize
			$html_form .= '<tr><td>PHP upload max filesize</td><td>'.$suSU->i_upload_max_filesize().'</td></tr>';
			
			// www write perms
			$b_write_perms = $suSU->b_root_write_permission();
			
			$s_write_perms = ($b_write_perms ? '<i class="green glyphicon glyphicon-ok"></i>' : '<i class="red glyphicon glyphicon-remove"></i>');
			$html_form .= '<tr><td>www write permission</td><td>'.$s_write_perms.'</td></tr>';

			/*
			if(!$b_write_perms){
				foreach ($suSU->sa_unwritable_dirs as $s_path) {
					$html_form .= $s_path.'<br/>';
				}				
			}
			*/

			// mod_rewrite enabled
			$b_mod_rewrite = $suSU->b_mod_rewrite_enabled();
			$s_mod_rewrite = ($b_mod_rewrite ? '<i class="green glyphicon glyphicon-ok"></i>' : '<i class="red glyphicon glyphicon-remove"></i>');
			$html_form .= '<tr><td>mod_rewrite enabled</td><td>'.$s_mod_rewrite.'</td></tr>';

			$html_form .= '</tbody></table>';


			$html_form .= '<hr/>';

			$s_update_enabled = ($b_write_perms ? '' : ' disabled');

			$html_form .= '<a class="btn btn-info" href="'.S_BASE_EXTENSION.'flot-admin/admin/update.php" target="_blank" '.$s_update_enabled.'><i class="glyphicon glyphicon-cloud-download"></i> update flot</a>';


			$html_form .= '<hr/>';
			$html_form .= '</div>';
			$html_form .= '</div>';
			
			# save
			$html_form .= '<div class="form-group">';

			$html_form .= '<button type="submit" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-disk"></i> save</button>';
			$html_form .= '</div>';

			# hidden elements
			$html_form .= '<input type="hidden" name="section" value="settings">';

			$html_form .= '</form>';

			return $html_form;
		}
		function html_make_error_page(){
			$fu_FileUtil = new FileUtilities;
			return $fu_FileUtil->s_errors();
		}
		function html_make_page_add_button(){
			$s_oncologies = '<li><a href="#" class="btn disabled">no page types :(</a></li>';

			$odOD = new OncologyData;

			$oa_oncologies_available = $odOD->oa_oncologies_available();

			if(count($oa_oncologies_available) > 0){
				$s_oncologies = '';
				foreach ($oa_oncologies_available as $key => $value) {
					$s_oncologies .= '<li><a href="'.S_BASE_EXTENSION.'flot-admin/admin/index.php?section=items&oncology='.$key.'&action=new">'.$value.'</a></li>';
				}
			}
			
			$html_add_content_button = '<div class="btn-group edit_item_general_toolbar">
				        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-plus"></i>&nbsp;Add <span class="hidden-xs"> a page</span>
				          <span class="caret"></span>
				        </button>
				        <ul class="dropdown-menu" role="menu">'.$s_oncologies.'</ul>
				      </div>';

			return $html_add_content_button;
		}
	}
?>