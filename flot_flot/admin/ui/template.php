<!DOCTYPE html>
<html>
	<head>
		<?php
			echo $html_header;
		?>
	</head>
	<body class="<?php echo $s_body_class; ?>">

		<div id="admin_header" class="clearer">
			<div class="left_col clearer">
				<a href="/flot_flot/admin/">
					<div id="logo_container">
						<span id="f" class="blue">f<span class="small-hidden">lot</span></span>
						<span id="l" class="red">f<span class="small-hidden">lot</span></span>
						<span id="o" class="yellow">f<span class="small-hidden">lot</span></span>
						<span id="t" class="green">f<span class="small-hidden">lot</span></span>
					</div>
				</a>
			</div>
			<div class="right_col">
				<div id="top_right_buttons" class="clearer">
					<a class="btn btn-danger btn-sm" href="logout.php"><i class="glyphicon glyphicon-user"></i> logout</a>
				</div>
			</div>
		</div>

		<div>
			<!-- tabs left -->
			<div class="left_col" id="left_admin_menu_col">
				<!-- render left menu, one item should be 'active' -->
				<?php echo $html_left_menu; ?>
			</div>
			<div class="right_col clearer" id="right_admin_section">
				<!-- main 'content' section -->
				<?php 
					if($html_make_admin_content_menu !== ""){
						echo '<div class="btn-group">'.$html_make_admin_content_menu.'</div>'; 
						echo "<hr/>";
					}
				?>
				<?php echo $html_make_admin_content; ?>
			</div>
		</div>
	</body>
</html>