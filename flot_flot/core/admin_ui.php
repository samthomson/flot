<?php
	# error handler


	class AdminUI {
		public $s_base_path;

		function __construct() {
			$this->s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
		}
		function html_make_left_menu(){
			return '<ul>
					<li class="active"><a href="/flot_flot/admin/index.php?section=items&oncology=page"><i class="glyphicon glyphicon-file"></i><span class="hidden-xs"> Webpages</span></a></li>
					<li><a href="/flot_flot/admin/index.php?section=pictures"><i class="glyphicon glyphicon-picture"></i><span class="hidden-xs"> Pictures</span></a></li>
					<li><a href="/flot_flot/admin/index.php?section=menus"><i class="glyphicon glyphicon-list"></i><span class="hidden-xs"> Menus</span></a></li>
					<li><a href="/flot_flot/admin/index.php?section=settings"><i class="glyphicon glyphicon-cog"></i><span class="hidden-xs"> Settings</span></a></li>
				</ul>';
		}
		function html_make_admin_page($html_header, $html_left_menu, $html_make_admin_content){
			/*
			global $html_header = $html_header;
			global $html_left_menu;
			global $html_make_admin_content;
			*/

			include($this->s_base_path.'flot_flot/admin/ui/template.php');
			exit();
		}
	}
?>