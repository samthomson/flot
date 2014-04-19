<?php
	# manage site

	require_once('../core/flot.php');


	$flot = new Flot;

	if($flot->b_is_user_admin()){

	}else{
		# forward them to login page
		$flot->_page_change("flot_flot/admin/login.php");
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
	</body>
</html>