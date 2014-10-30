<?php
	# log in and forward user to route admin section
	require('../core/flot.php');

	$flot = new Flot;
	$AdminUI = new AdminUI;

	# are we handling the log in form submission ?
	
	$flot->_handle_auth_attempt();


	if($flot->b_is_user_admin()){
		# they're logged in so forward them to the admin page to manage the site
		$flot->_page_change("index.php");
	}
	# serve them the log in form
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
			echo $AdminUI->s_admin_header();
		?>
	</head>
	<body>
		<div class="container" style="max-width:300px;margin-top:150px;">
			<!-- instruction, email, password, login/submit button -->
			<form role="form" method="post" name="login" action="login.php">
				<div class="form-group">
					<h1>login</h1>
				</div>
				<div class="form-group">
					<input type="email" name="email" class="form-control" id="login_email" placeholder="email">
				</div>
				<div class="form-group">
					<input type="password" name="password" class="form-control" id="login_email" placeholder="password">
				</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-success form-control" id="login_button">login</button>
				</div>
				<div class="form-group">
					<a href="/">back to site</a>
				</div>
			</form>
		</div>
	</body>
</html>