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
					<img src="/flot_flot/admin/images/flot.png" style="
					    max-height: 35px;
					    margin-left: 20px;
					"/>
				</a>
			</div>
			<div class="right_col">
				<div id="top_right_buttons" class="clearer">
					<?php echo $html_add_content_button; ?>
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
				<?php echo $html_make_admin_content; ?>
			</div>
		</div>
	</body>
</html>