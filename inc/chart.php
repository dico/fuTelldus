<?php
    

    if (!$telldusKeysSetup) {
        echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user'>your userprofile</a>.";
        exit();
    }



    /* Get chart type
    --------------------------------------------------------------------------- */
    if (empty($user['chart_type'])) $chartType = "mergeHCharts";
    else $chartType = $user['chart_type'];

    if (isset($_GET['charttype'])) {
        $chartType = clean($_GET['charttype']);
    }

echo "<script>console.log( 'Chart type: $chartType' );</script>";


    /* Headline
    --------------------------------------------------------------------------- */
    echo "<h3>{$lang['Chart']}</h3>";




    /* Toggle
    --------------------------------------------------------------------------- */
    echo "<div style='float:right; margin-top:-45px; margin-right:20px;' class='btn-group'>";

            echo "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#''>";
                echo "{$lang['Action']}";
                echo "<span class='caret'></span>";
            echo "</a>";

            echo "<ul class='dropdown-menu'>";
		echo "<li><a href='?page=chart&charttype=highcharts'>Single Highcharts</a></li>";
		echo "<li><a href='?page=chart&charttype=mergeHCharts'>{$lang['Combine charts']} Highcharts</a></li>";
		echo "<li><a href='?page=chart&charttype=rgraph'>Single RGraph</a></li>";
		echo "<li><a href='?page=chart&charttype=mergeCharts'>{$lang['Combine charts']} RGraph</a></li>";
                echo "<li><a href='?page=report'>{$lang['Report']} (RGraph)</a></li>";
            echo "</ul>";
        echo "</div>";


    /* Include chart
    --------------------------------------------------------------------------- */
        if ($chartType == "rgraph") {
            include("inc/chart_rgraph.php");
        }

        elseif ($chartType == "highcharts") {
            include("inc/chart_highchart.php");
        }

        elseif ($chartType == "mergeCharts") {
            include("inc/chart_rgraph_mergeSensors.php");
        }

        elseif ($chartType == "mergeHCharts") {
            include("inc/chart_highchart_mergeSensors.php");
        }

        else {
            echo "Something went wrong.. Could'n determine chart to display. Try selecting chart in your userprofile.";
        }

?>
