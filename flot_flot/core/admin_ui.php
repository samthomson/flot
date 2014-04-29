<?php
	# error handler


	class AdminUI {
		public $s_base_path;

		function __construct() {
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
		}
		function html_make_left_menu($s_active_section){

			return '<div id="admin_menu_left">
					<a class="admin_menu_left'.$this->s_active_or_empty("items", $s_active_section).'" href="/flot_flot/admin/index.php?section=items&amp;oncology=page"><i class="glyphicon glyphicon-file"></i><span class="small-hidden condensed_hidden"> Webpages</span></a>
					<a class="admin_menu_left'.$this->s_active_or_empty("pictures", $s_active_section).'" href="/flot_flot/admin/index.php?section=pictures"><i class="glyphicon glyphicon-picture"></i><span class="small-hidden condensed_hidden"> Pictures</span></a>
					<a class="admin_menu_left'.$this->s_active_or_empty("menus", $s_active_section).'" href="/flot_flot/admin/index.php?section=menus"><i class="glyphicon glyphicon-list"></i><span class="small-hidden condensed_hidden"> Menus</span></a>
					<a class="admin_menu_left'.$this->s_active_or_empty("settings", $s_active_section).'" href="/flot_flot/admin/index.php?section=settings"><i class="glyphicon glyphicon-cog"></i><span class="small-hidden condensed_hidden"> Settings</span></a>
				</div>';
		}
		function html_make_admin_page($html_header, $html_left_menu, $html_make_admin_content, $html_make_admin_content_menu, $s_body_class){

			include($this->s_base_path.'flot_flot/admin/ui/template.php');
			exit();
		}
		function s_active_or_empty($s_me, $s_current){
			if($s_me === $s_current)
				return " active";
			else
				return "";
		}
		function s_admin_header($s_section = ""){
			$s_header = "";

			$s_header .= $this->html_admin_headers_base();

			if($s_section === "items"){
				# ckeditor
				$s_header .= '<script src="/flot_flot/external_integrations/ckeditor/ckeditor.js"></script>';

				# general admin js
				$s_header .= '<script src="/flot_flot/admin/js/admin_itemedit.js"></script>';
			}

			if($s_section === "pictures"){
				# general admin js
				$s_header .= $this->html_admin_headers_pictures();
			}


			$s_header .= '<title>flot - manage your site</title>';

			return $s_header;
		}
		function html_admin_headers_pictures(){
			$s_header = "";
			$s_header .= '<script src="/flot_flot/admin/js/jquery.ui.widget.js"></script>';
			$s_header .= '<script src="/flot_flot/admin/js/jquery.iframe-transport.js"></script>';
			$s_header .= '<script src="/flot_flot/admin/js/jquery.fileupload.js"></script>';
			$s_header .= '<script src="/flot_flot/admin/js/admin_pictures.js"></script>';
			return $s_header;
		}
		function html_admin_headers_base(){
			$s_header = "";
			# bootstrap css
			$s_header .= '<link rel="stylesheet" href="/flot_flot/admin/css/bootstrap.min.css">';
			# admin css
			$s_header .= '<link rel="stylesheet" href="/flot_flot/admin/css/admin_style.css">';

			# jquery js
			$s_header .= '<script src="/flot_flot/admin/js/jquery.min.js"></script>';
			# bootstrap js
			$s_header .= '<script src="/flot_flot/admin/js/bootstrap.min.js"></script>';
			return $s_header;
		}
	}
?>