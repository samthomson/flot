<?php
	# log in and forward user to route admin section
	$s_base_path = str_replace($_SERVER['SCRIPT_NAME'],"",str_replace("\\","/",$_SERVER['SCRIPT_FILENAME'])).'/';
	require($s_base_path.'/flot_flot/core/flot.php');

	$flot = new Flot;
	$requirements = new FlotRequirements;

	# are we handling the form submission?
	if($flot->b_post_vars()){
		// user has entered an email and password okay
		if(isset($_POST["email"]) && isset($_POST["password"])){
			// are the requirements met?
			if($requirements->b_requirements_met()){
				//
				// set up flot
				//
				// add username/pass, store to datastore
				$flot->datastore->_add_user($_POST["email"], $_POST["password"]);

				# at a later date, add some starter items
				## web page oncology
				## a few pages
				## a menu

				// generate all pages
				$flot->_render_all_pages();

				// delete this start.php page for security
				$flot->_delete_start_page();

				// redirect user to home page
				$flot->_page_change("/");
			}
		}
	}


?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		
		<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css'>

		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

		<style type="text/css">
			body{
				font-family: 'Nunito', sans-serif !important;
				word-wrap:break-word;
			}

			#logo_container{	
				display: block;
				height:180px;
				width: 250px;
			}
			#logo_container span{
				position: absolute;
				font-size: 160px;
			}
			#logo_container span#f{z-index: 13;}
			#logo_container span#l{z-index: 12;margin-left: -3px;margin-top: -3px;}
			#logo_container span#o{z-index: 11;margin-left: 4px;margin-top: -2px;}
			#logo_container span#t{z-index: 10;margin-left: -3px;margin-top: 3px;}

			.blue {
			  color: #0074d9; }
			.green {
			  color: #2ecc40; }
			.yellow {
			  color: #ffdc00; }
			.red {
			  color: #ff4136; }
		</style>
	</head>
	<body>
		<div class="container" style="max-width:300px;margin-top:50px;">
			<!-- instruction, email, password, submit button -->
			<form role="form" method="post" name="login" action="start.php">
				<div class="form-group">
					<div id="logo_container" class="clearfix">
						<span id="f" class="blue">flot</span>
						<span id="l" class="red">flot</span>
						<span id="o" class="yellow">flot</span>
						<span id="t" class="green">flot</span>
					</div>
				</div>
				<?php
				    if($requirements->b_requirements_met()){
				    	?>
						<div class="form-group">
							<p>enter an email and password to set up your new website, you'll use these to login to flot</p>
						</div>
						<div class="form-group">
							<input type="email" name="email" class="form-control" id="flot_email" placeholder="email">
						</div>
						<div class="form-group">
							<input type="password" name="password" class="form-control" id="flot_password" placeholder="password">
						</div>
						<div class="form-group">
							<button type="submit" name="submit" class="btn btn-success form-control" id="login_button">start flot</button>
						</div>

					<?php }else{ ?>

					<div class="alert alert-danger">
					<h4><i class="glyphicon glyphicon-flag"></i> flot can't be installed until the following steps are taken</h4>
					<ul>
					<?php
						foreach ($requirements->sa_requirements_to_remedy() as $req) {
							echo "<li>$req</li>";
						}
					?>
					</ul>
					</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-success form-control" id="login_button">try again</button>
				</div>

					<?php }  ?>

			</form>
		</div>
	</body>
</html>