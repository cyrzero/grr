<?php
/**
 * menu_gauche.php
 * Menu calendrier & domaines & ressource & légende
 * Ce script fait partie de l'application GRR
 * Dernière modification : $Date: 2018-05-17 12:00$
 * @author    Laurent Delineau & JeromeB & Yan Naessens
 * @copyright Copyright 2003-2018 Team DEVOME - JeromeB
 * @link      http://www.gnu.org/licenses/licenses.html
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */
 
 // Récupération de la valeur de default_language afin d'avoir un calendrier dynamique pour les langues
if (isset($_GET['default_language'])){$_SESSION['default_language'] = $_GET['default_language'];}
$language_session = $_SESSION['default_language'];
 
if ($_GET['pview'] != 1)
{
	$path = $_SERVER['PHP_SELF'];
	$file = basename ($path);
	if ( $file== 'month_all2.php' or Settings::get("menu_gauche") == 0){
		echo '<div id="menuGaucheMonthAll2">';
	} elseif ( Settings::get("menu_gauche") == 2){
		echo '<div class="col-lg-12 col-md-12 col-xs-12">';
	} else{
		echo '<div class="col-lg-3 col-md-12 col-xs-12">';
	}
	echo '<div id="menuGauche">';

	$pageActuel = str_replace(".php","",basename($_SERVER['PHP_SELF']));
    // détermine le contexte d'appel : jour, semaine ou mois
    $pageSimple = str_replace(".php","",$file);
    $pageSimple = str_replace("_all","",$pageSimple);
    $pageSimple = str_replace("2","",$pageSimple);
    if ($pageSimple == "day") {
        $pageTout = "day.php";
    }
    else $pageTout = $pageSimple."_all.php";
    // $pageSimple .= '.php';
?>

<!-- Calendrier en JQuery/Ajax avec gestion des langues via le navigateur -->
<style>
#datepicker-container{
  text-align:center;
}
#datepicker-center{
  display:inline-block;
  margin:0 auto;
  padding: 10px;
}
div.ui-datepicker{
 font-size:110%;
}
</style>

		<div id="datepicker-container">
		  <div id="datepicker-center">
			<div id="calendar"></div>
		  </div>
		</div>
	
    <script type='text/javascript'>
		
		function getQueryVariable(variable)
		{
		   var query = window.location.search.substring(1);
		   var vars = query.split("&");
		   for (var i=0;i<vars.length;i++) {
				   var pair = vars[i].split("=");
				   if(pair[0] == variable){return pair[1];}
		   }
		   return(false);
		}
		
		$(document).ready(function() {  
  
            var userLang = navigator.language || navigator.userLanguage;  
  
            var options = $.extend({},   
                $.datepicker.regional[userLang], {  
                    dateFormat: 'dd-mm-yy',
					inline: true,
					showWeek: true,
					changeMonth: true,
					changeYear: true,
					onSelect: function(dateText, inst) { 
						var date = $(this).datepicker('getDate'),
							day  = date.getDate(),  
							month = date.getMonth() + 1,              
							year =  date.getFullYear();
						var area = getQueryVariable("area");
						var hostname = window.location.host;
						var protocol = window.location.protocol;
						self.location.replace(protocol +"//"+ hostname +"/day.php?area="+ area +"&day="+ day +"&year="+ year +"&month="+ month);
						}  
                } 
            );  
  
            $("#calendar").datepicker(options);  
            
            $(function() {	
				$( "#datepicker" ).datepicker({
			});
			
			// Highlight week on hover week number
			$(document).on("mouseenter",".ui-datepicker-week-col",
						   function(){$(this).siblings().find("a").addClass('ui-state-hover');} );
			$(document).on("mouseleave",".ui-datepicker-week-col",
						   function(){$(this).siblings().find("a").removeClass('ui-state-hover');} );
			
			// Select week on click on week number
			$(document).on("click",".ui-datepicker-week-col",
			   function(){
				   $first = $(this).siblings().find("a").first();
				   $first.click();
				   $parentFirst = $first.parent();
				   var area = getQueryVariable("area");
				   var hostname = window.location.host;
				   var protocol = window.location.protocol;
				   var day  = $first.text(),  
					   month = $parentFirst.data("month")+1,              
					   year =  $parentFirst.data("year");
				   self.location.replace(protocol +"//"+ hostname +"/week_all.php?area="+ area +"&day="+ day +"&year="+ year +"&month="+ month);
					});
				});
        });
		

	</script>

<?php
    // Calendrier
	//minicals($year, $month, $day, $area, $room, $pageActuel);
	
	// Liste sites, domaines, ressources
	if (isset($_SESSION['default_list_type']) || (Settings::get("authentification_obli") == 1))
		$area_list_format = $_SESSION['default_list_type'];
	else
		$area_list_format = Settings::get("area_list_format");

	if(Settings::get("menu_gauche") == 2){
		echo "\n<div class=\"col-lg-3 col-md-12 col-xs-12\">\n".PHP_EOL;
	} else{
		echo "\n<div class=\"col-lg-12 col-md-12 col-xs-12\">\n".PHP_EOL;
	}

	if ($area_list_format != "list")
	{
		if ($area_list_format == "select")
		{
			echo make_site_select_html($pageTout, $id_site, $year, $month, $day, getUserName());
			echo make_area_select_html($pageTout, $id_site, $area, $year, $month, $day, getUserName());
			echo make_room_select_html($pageSimple, $area, $room, $year, $month, $day);
		}
		else
		{
			echo make_site_item_html($pageTout, $id_site, $year, $month, $day, getUserName());
			echo make_area_item_html($pageTout,$id_site, $area, $year, $month, $day, getUserName());
			echo make_room_item_html($pageSimple, $area, $room, $year, $month, $day);
		}
	}
	else
	{
		echo make_site_list_html($pageTout,$id_site,$year,$month,$day,getUserName());
		echo make_area_list_html($pageTout,$id_site, $area, $year, $month, $day, getUserName());
		echo make_room_list_html($pageSimple, $area, $room, $year, $month, $day);
	}

	echo "\n</div>\n".PHP_EOL;

	//Legende
	if (Settings::get("legend") == '0'){
		if(Settings::get("menu_gauche") == 2){
			echo "\n<div class=\"col-lg-3 col-md-12 col-xs-12\">\n".PHP_EOL;
		} else{
			echo "\n<div class=\"col-lg-12 col-md-12 col-xs-12\">\n".PHP_EOL;
		}
		show_colour_key($area);
		echo "\n</div>\n".PHP_EOL;
	}

	//
	echo '</div>';
	echo '</div>';

}
?>
