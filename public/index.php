<?php
	require("../lib/base.inc.php");


	/* Check for public sensors
	--------------------------------------------------------------------------- */
	$query = "SELECT * FROM ".$db_prefix."sensors WHERE monitoring='1' AND public='1'";
    $result = $mysqli->query($query);
    $numRows = $result->num_rows;

    if ($numRows == 0) {
    	header("Location: ../login/?msg=03");
    	exit();
    }

    //while ($row = $result->fetch_array()) {


    /* Get public language
	--------------------------------------------------------------------------- */
	include("../lib/languages/".$config['public_page_language'].".php");

?>


<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="utf-8">
    <title><?php echo $config['pagetitle']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">


	

	<!-- Jquery -->
	<script src="../lib/packages/jquery/jquery-1.9.1.min.js"></script>

	<script src="../lib/packages/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
	<link href="../lib/packages/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet">

	<script src="../lib/packages/timeago_jquery/jquery.timeago.js"></script>
	<?php
		if ($defaultLang == "no") echo "<script src=\"../lib/packages/timeago_jquery/jquery.timeago.no.js\"></script>";
	?>


	<!-- Bootstrap framework -->
	<script src="../lib/packages/bootstrap/js/bootstrap.min.js"></script>
	<link href="../lib/packages/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="../lib/packages/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">


	<link href="../css/pagestyle.css" rel="stylesheet">


	<!-- For iPhone 4 Retina display: -->
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../images/thermometer.png">

	<!-- For iPad: -->
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../images/thermometer.png">

	<!-- For iPhone: -->
	<link rel="apple-touch-icon-precomposed" href="../images/thermometer.png">




	<script type="text/javascript">
		idleTime = 0;
		
		$(document).ready(function () {
		    //Increment the idle time counter every minute.
		    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

		    //Zero the idle timer on mouse movement.
		    $(this).mousemove(function (e) {
		        idleTime = 0;
		    });
		    $(this).keypress(function (e) {
		        idleTime = 0;
		    });
		});
		
		function timerIncrement() {
		    idleTime = idleTime + 1;
		    if (idleTime > 19) { // 20 minutes
		        window.location.reload();
		    }
		}
	</script>

</head>
<body>

	<div class="container">




		<!-- HEADER: MOBILE -->
		<div class="masthead visible-phone" style='background-color:#0088cc; margin:-20px -20px 15px -20px'>
			<div style='padding:5px 5px;'>

				<div class="pull-left" style='font-size:22px; font-weight:bold; color:#fff; padding:3px 5px;'>
					<a style='color:#fff;' href='index.php'>
						<?php echo $config['pagetitle']; ?>
					</a>
				</div>

				<div class="btn-group pull-right">
				  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    <i class="icon-th-list"></i>
				    <span class="caret"></span>
				  </a>
				  <ul class="dropdown-menu">
				    <li><a href='login'><?php echo $lang['Login']; ?></a></li>
				  </ul>
				</div>

				<div class='clearfix'></div>
			</div>
		</div>
		<!-- END HEADER: MOBILE -->





		<!-- HEADER: PAD AND DESKTOP -->
		<div class="masthead hidden-phone">
			
			<h3 class="muted">
				<a href='index.php'>
					<img style='height:30px;' src="../images/logo.jpg" alt='logo' />
					<?php echo $config['pagetitle']; ?>
				</a>
			</h3>

			<div style="float:right; margin-top:-45px; margin-right:15px;">
				<div class="btn-group">					
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<?php
							if (isset($_SESSION['fuTelldus_user_loggedin'])) {
								echo $user['mail'];
							} else {
								echo $lang['Not logged in'];
							}
						?>
						<span class="caret"></span>
					</a>

					<ul class="dropdown-menu">
						<?php
							if (isset($_SESSION['fuTelldus_user_loggedin'])) {
								echo "<li><a href='../login/'>".$lang['Admin']."</a></li>";
								echo "<li><a href='../login/logout.php'>".$lang['Log out']."</a></li>";
							} else {
								echo "<li><a href='../login/'>".$lang['Login']."</a></li>";
							}
							
						?>
					</ul>
				</div>

			</div>
		</div>
		<!-- END HEADER: PAD AND DESKTOP -->




		<?php include("include_script.inc.php"); ?>


		<div class='clearfix'></div>

		<div class='hidden-phone' style='text-align:center; border-top:1px solid #eaeaea; font-size:10px; margin-top:35px; color:#c7c7c7;'>
			Developed by <a href='http://www.fosen-utvikling.no'>Fosen Utvikling</a> &nbsp;&nbsp;
			Last load: <?php echo date("d-m-Y H:i"); ?>

			<br />
			This work is licensed under a <a href='http://creativecommons.org/licenses/by-nc/3.0/'>Creative Commons Attribution-NonCommercial 3.0 Unported License</a>.
		</div>


	</div> <!-- /container -->

</body>
</html>