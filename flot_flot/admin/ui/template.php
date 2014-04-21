<!DOCTYPE html>
<html>
	<head>
		<?php
			echo $html_header;
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
			<div class="col-xs-1 col-sm-3">
				<!-- render left menu, one item should be 'active' -->
				<?php echo $html_left_menu ?>
			</div>
			<div class="col-xs-11 col-xs-9">
				<!-- main 'content' section -->

				<?php 
					if($html_make_admin_content_menu !== "")
					echo $html_make_admin_content_menu; 
				?>
				<?php echo $html_make_admin_content; ?>
		        </div>
			</div>
		</div>
	</body>
</html>