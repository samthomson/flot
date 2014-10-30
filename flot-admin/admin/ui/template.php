<!DOCTYPE html>
<html ng-app="flot">
	<head>
		<?php
			echo $html_header;
		?>
	</head>
	<body class="<?php echo $s_body_class; ?>">


		<!-- Reusable Modal -->
		<!-- functions for controlling can be found in admin.js #_reusable_modal_stuff -->
		<div class="modal fade" id="flot_modal" tabindex="-1" role="dialog" aria-labelledby="flot_modal_label" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="flot_modal_label"></h4>
					</div>
					<div class="modal-body">
		  				<div class="" id="flot_modal_body">

		  				</div>
		  			</div>
					<div class="modal-footer" id="flot_modal_footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>




		<div id="admin_header" class="clearer">
			<div class="left_col clearer">
				<a href="/flot-admin/admin/">
					<?php $suSU = new SettingsUtilities; ?>
					<img src="<?php echo S_BASE_EXTENSION; ?>flot-admin/admin/images/flot.png" style="
					    max-height: 35px;
					    margin-left: 20px;
					" alt="flot version <?php echo $suSU->s_literal_flot_version(); ?>" title="flot version <?php echo $suSU->s_literal_flot_version(); ?>"/>
				</a>
			</div>
			<div class="right_col">
				<div id="top_right_buttons" class="clearer">
					<?php echo $html_add_content_button; ?>
					<a class="btn btn-danger btn-sm" href="logout.php"><i class="glyphicon glyphicon-user"></i> logout</a>
				</div>
			</div>
		</div>

		<div id="left_right_container">
			<!-- tabs left -->
			<div class="left_col" id="left_admin_menu_col">
				<!-- render left menu, one item should be 'active' -->
				<?php echo $html_left_menu; ?>
			</div>
			<div class="right_col clearer" id="right_admin_section">
				<?php echo $html_message_alert; ?>
				
				<!-- main 'content' section -->
				<?php echo $html_make_admin_content; ?>
			</div>
		</div>
	</body>
</html>