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
	}
?>