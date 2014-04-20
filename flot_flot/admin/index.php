<?php
	# manage site

	require_once('../core/flot.php');


	$flot = new Flot;

	if(!$flot->b_is_user_admin()){
		# forward them to login page
		$flot->_page_change("/flot_flot/admin/login.php");
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<?php
			echo $flot->s_admin_header();
		?>
	</head>
	<body>




		<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">flot</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> help</a></li>
        <li><a href="logout.php"><i class="glyphicon glyphicon-user"></i> logout</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


<div class="row">

	<!-- tabs left -->


<?php
	$s_section = "items";
	if(isset($_GET['section'])){
		switch ($_GET['section']) {
			case 'items':
				$s_section = "items";
				break;
			case 'pictures':
				$s_section = "pictures";
				break;
			case 'menus':
				$s_section = "menus";
				break;
			case 'settings':
				$s_section = "settings";
				break;
			
			default:
				$s_section = "items";
				break;
		}
	}

?>



	<div class="col-xs-1 col-sm-3">
		<!-- render left menu, one item should be 'active' -->
		<ul>
			<li class="active"><a href="/flot_flot/admin/index.php?section=items&oncology=page"><i class="glyphicon glyphicon-file"></i><span class="hidden-xs"> Webpages</span></a></li>
			<li><a href="/flot_flot/admin/index.php?section=pictures"><i class="glyphicon glyphicon-picture"></i><span class="hidden-xs"> Pictures</span></a></li>
			<li><a href="/flot_flot/admin/index.php?section=menus"><i class="glyphicon glyphicon-list"></i><span class="hidden-xs"> Menus</span></a></li>
			<li><a href="/flot_flot/admin/index.php?section=settings"><i class="glyphicon glyphicon-cog"></i><span class="hidden-xs"> Settings</span></a></li>
		</ul>
	</div>
	<div class="col-xs-11 col-xs-9">
		<!-- main 'content' section -->
		<?php
			switch($s_section){
				case "items":
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

		         	echo $hmtl_pages_ui;
					break;
				case "pictures":
					$html_pictures_ui = "pictures";
					echo $html_pictures_ui;
					break;
				case "menus":
					$html_menu_ui = "menus";
					echo $html_menu_ui;
					break;
				case "settings":
					$html_settings_ui = "settings";
					echo $html_settings_ui;
					break;
			}
         	?>
         </div>
	</div>
</div>
	</body>
</html>