<?php


  	/* Get parameters
  	--------------------------------------------------------------------------- */
  	if (isset($_GET['id'])) 		  $getID 	= clean($_GET['id']);
  	if (isset($_GET['action'])) 	$action = clean($_GET['action']);
  	

    if (isset($_GET['view'])) {
      $view = clean($_GET['view']);
    } else {
      header("Location: ?page=settings&view=user&action=edit&id={$user['user_id']}");
      exit();
    }

  ?>


  <div class="container-fluid">


    <div class="row-fluid">
      <div class="span3">
        <div class="well sidebar-nav">
          <ul class="nav nav-list">



            <li class="nav-header"><?php echo $lang['Settings']; ?></li>

            <?php
              if ($view == "general") $vActive_general = "active";
              if ($view == "user") $vActive_user = "active";
              if ($view == "share") $vActive_share = "active";
              if ($view == "notifications") $vActive_notifications = "active";
              if ($view == "schedule") $vActive_schedule = "active";
              if ($view == "cron") $vActive_cron = "active";
              if (substr($view, 0, 12) == "telldus_test") $vActive_telldusTest = "active";
              if (substr($view, 0, 5) == "users") $vActive_users = "active";




              echo "<li class='$vActive_user'><a href='?page=settings&view=user&action=edit&id={$user['user_id']}'>{$lang['Userprofile']}</a></li>";
              echo "<li class='$vActive_share'><a href='?page=settings&view=share'>{$lang['Shared sensors']}</a></li>";
              echo "<li class='$vActive_schedule'><a href='?page=settings&view=schedule'>{$lang['Schedule']}</a></li>";
              //echo "<li class='$vActive_telldusTest'><a href='?page=settings&view=telldus_test'>{$lang['Telldus connection test']}</a></li>";

              if ($user['admin'] == 1) {
                echo "<li class='nav-header'>Admin</li>";

                echo "<li class='$vActive_general'><a href='?page=settings&view=general'>".$lang['Page settings']."</a></li>";
                echo "<li class='$vActive_users'><a href='?page=settings&view=users'>".$lang['Users']."</a></li>";
                echo "<li class='$vActive_cron'><a href='?page=settings&view=cron'>".$lang['Test cron-files']."</a></li>";
              }
            ?>



          </ul>
        </div><!--/.well -->
      </div><!--/span-->



      <div class="span9">
      	<?php
      		if (isset($_GET['view'])) {
      			include("inc/settings_" . $view . ".php");
      		} else {
      			include("inc/settings_general.php");
      		}
      	?>
      </div>
    </div>



  </div>