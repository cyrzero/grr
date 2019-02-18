<?php
/**
 * month_all2.php
 * Interface d'accueil avec affichage par mois des réservations de toutes les ressources d'un domaine
 * Ce script fait partie de l'application GRR
 * Dernière modification : $Date: 2018-06-29 15:00$
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
// cette page est partiellement internationalisée : à compléter

$grr_script_name = "month_all2.php";

if (!isset($_GET['day'])){ // pour l'affichage du mois la variable jour n'est pas obligatoire dans l'url, cependant necessaire pour setdate.php
	$_GET['day'] = 1;
}

include "include/planning_init.inc.php";


$month_start = mktime(0, 0, 0, $month, 1, $year);
$weekday_start = (date("w", $month_start) - $weekstarts + 7) % 7;
$days_in_month = date("t", $month_start);
$month_end = mktime(23, 59, 59, $month, $days_in_month, $year);
if ($enable_periods=='y')
{
	$resolution = 60;
	$morningstarts = 12;
	$eveningends = 12;
	$eveningends_minutes = count($periods_name)-1;
}
$this_area_name = "";
$this_room_name = "";
$this_area_name = grr_sql_query1("SELECT area_name FROM ".TABLE_PREFIX."_area WHERE id=$area");
$i = mktime(0,0,0,$month-1,1,$year);
$yy = date("Y",$i);
$ym = date("n",$i);
$i = mktime(0,0,0,$month+1,1,$year);
$ty = date("Y",$i);
$tm = date("n",$i);

echo "<div id='planningMonthAll2' style='width:133%' >";
echo "<div class=\"titre_planning\"><table class=\"table-header\">";
if ((!isset($_GET['pview'])) or ($_GET['pview'] != 1))
{
	echo "\n
	<tr>
		<td class=\"left\">
			<button class=\"btn btn-default btn-xs\" onclick=\"charger();javascript: location.href='month_all2.php?year=$yy&amp;month=$ym&amp;area=$area';\" \"/><span class=\"glyphicon glyphicon-backward\"></span> ".get_vocab("monthbefore")." </button>
		</td>";

		echo " <td>";
		include "include/trailer.inc.php";
		echo "</td>

		<td class=\"right\">
			<button class=\"btn btn-default btn-xs\" onclick=\"charger();javascript: location.href='month_all2.php?year=$ty&amp;month=$tm&amp;area=$area';\" \"/>".get_vocab('monthafter')." <span class=\"glyphicon glyphicon-forward\"></button>
		</td>
	</tr>";

	echo "<tr>";
	echo "<td class=\"left\"> ";
	$month_all2 = 1;
    /*
	echo "<div id='voir'><input type=\"button\" class=\"btn btn-default btn-xs\" value=\"Afficher le menu à gauche.\" onClick=\"divaffiche($month_all2)\" /></div> ";
    echo "<div id='cacher'><input type=\"button\" class=\"btn btn-default btn-xs\" value=\"Cacher le menu à gauche.\" onClick=\"divcache($month_all2)\" /></div> "; */
    echo "<div id='voir'><button class=\"btn btn-default btn-sm\" onClick=\"divaffiche($month_all2)\" title='".get_vocab('show_left_menu')."'><span class=\"glyphicon glyphicon-chevron-right\"></span></button></div> ";
    echo "<div id='cacher'><button class=\"btn btn-default btn-sm\" onClick=\"divcache($month_all2)\" title='".get_vocab('hide_left_menu')."'><span class=\"glyphicon glyphicon-chevron-left\"></span></button></div> "; 
	echo "</td>";
}
echo " <td>";
// echo "<h4 class=\"titre\">" . ucfirst(utf8_strftime("%B %Y", $month_start)). " ".ucfirst($this_area_name)." - ".get_vocab("all_areas")."</h4>";
echo '<h4 class="titre"> '. ucfirst($this_area_name).' - '.get_vocab("all_areas").'<br>'.ucfirst(utf8_strftime("%B ", $month_start)).'<a href="year.php" title="'.get_vocab('see_all_the_rooms_for_several_months').'">'.ucfirst(utf8_strftime("%Y", $month_start)).'</a></h4>'.PHP_EOL;
if ($_GET['pview'] != 1)
	echo " <a href=\"month_all.php?year=$year&amp;month=$month&amp;area=$area\"><span class='glyphicon glyphicon-refresh'></a>";
echo " </td>";
echo " </tr>";
echo "</table>";
echo "</div>\n";
if ($_GET['pview'] == 1 && $_GET['precedent'] == 1)
{
	echo "<span id=\"lienPrecedent\">
	<button class=\"btn btn-default btn-xs\" onclick=\"charger();javascript:history.back();\">Précedent</button>
</span>";
}
$all_day = preg_replace("/ /", " ", get_vocab("all_day"));
$sql = "SELECT start_time, end_time,".TABLE_PREFIX."_entry.id, name, beneficiaire, room_name, statut_entry, ".TABLE_PREFIX."_entry.description, ".TABLE_PREFIX."_entry.option_reservation, ".TABLE_PREFIX."_room.delais_option_reservation, type, ".TABLE_PREFIX."_entry.moderate
FROM ".TABLE_PREFIX."_entry inner join ".TABLE_PREFIX."_room on ".TABLE_PREFIX."_entry.room_id=".TABLE_PREFIX."_room.id
WHERE (start_time <= $month_end AND end_time > $month_start and area_id='".$area."')
ORDER by start_time, end_time, ".TABLE_PREFIX."_room.room_name";
$res = grr_sql_query($sql);
if (!$res)
	echo grr_sql_error();
else
{
	echo " <div class=\"contenu_planning\">";
	for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
	{
		$sql_beneficiaire = "SELECT prenom, nom FROM ".TABLE_PREFIX."_utilisateurs WHERE login='$row[4]'";
		$res_beneficiaire = grr_sql_query($sql_beneficiaire);
		if ($res_beneficiaire)
			$row_user = grr_sql_row($res_beneficiaire, 0);
		if ($debug_flag)
			echo "<br />DEBUG: result $i, id $row[2], starts $row[0], ends $row[1]\n";
		$t = max((int)$row[0], $month_start);
		$end_t = min((int)$row[1], $month_end);
		$day_num = date("j", $t);
		if ($enable_periods == 'y')
			$midnight = mktime(12,0,0,$month,$day_num,$year);
		else
			$midnight = mktime(0, 0, 0, $month, $day_num, $year);
		while ($t < $end_t)
		{
			if ($debug_flag)
				echo "<br />DEBUG: Entry $row[2] day $day_num\n";
			$d[$day_num]["id"][] = $row[2];
			$temp = "";
			if (Settings::get("display_info_bulle") == 1)
				$temp = get_vocab("reservee au nom de").$row_user[0]." ".$row_user[1];
			else if (Settings::get("display_info_bulle") == 2)
				$temp = $row[7];
			if ($temp != "")
				$temp = " - ".$temp;
			$d[$day_num]["who1"][] = affichage_lien_resa_planning($row[3],$row[2]);
			$d[$day_num]["room"][] = $row[5] ;
			$d[$day_num]["res"][] = $row[6];
			$d[$day_num]["color"][] = $row[10];
			if ($row[9] > 0)
				$d[$day_num]["option_reser"][] = $row[8];
			else
				$d[$day_num]["option_reser"][] = -1;
			$d[$day_num]["moderation"][] = $row[11];
			$midnight_tonight = $midnight + 86400;
			if ($enable_periods == 'y')
			{
				$start_str = preg_replace("/ /", " ", period_time_string($row[0]));
				$end_str   = preg_replace("/ /", " ", period_time_string($row[1], -1));
				switch (cmp3($row[0], $midnight) . cmp3($row[1], $midnight_tonight))
				{
					case "> < ":
					case "= < ":
					if ($start_str == $end_str)
						$d[$day_num]["data"][] = $start_str;
					else
						$d[$day_num]["data"][] = $start_str . get_vocab("to") . $end_str;
					break;
					case "> = ":
					$d[$day_num]["data"][] = $start_str . get_vocab("to")."24:00";
					break;
					case "> > ":
					$d[$day_num]["data"][] = $start_str . get_vocab("to")."&gt;";
					break;
					case "= = ":
					$d[$day_num]["data"][] = $all_day;
					break;
					case "= > ":
					$d[$day_num]["data"][] = $all_day . "&gt;";
					break;
					case "< < ":
					$d[$day_num]["data"][] = "&lt;".get_vocab("to") . $end_str;
					break;
					case "< = ":
					$d[$day_num]["data"][] = "&lt;" . $all_day;
					break;
					case "< > ":
					$d[$day_num]["data"][] = "&lt;" . $all_day . "&gt;";
					break;
				}
			}
			else
			{
				switch (cmp3($row[0], $midnight) . cmp3($row[1], $midnight_tonight))
				{
					case "> < ":
					case "= < ":
					$d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . get_vocab("to") . date(hour_min_format(), $row[1]);
					break;
					case "> = ":
					$d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . get_vocab("to")."24:00";
					break;
					case "> > ":
					$d[$day_num]["data"][] = date(hour_min_format(), $row[0]) . get_vocab("to")."&gt;";
					break;
					case "= = ":
					$d[$day_num]["data"][] = $all_day;
					break;
					case "= > ":
					$d[$day_num]["data"][] = $all_day . "&gt;";
					break;
					case "< < ":
					$d[$day_num]["data"][] = "&lt;".get_vocab("to") . date(hour_min_format(), $row[1]);
					break;
					case "< = ":
					$d[$day_num]["data"][] = "&lt;" . $all_day;
					break;
					case "< > ":
					$d[$day_num]["data"][] = "&lt;" . $all_day . "&gt;";
					break;
				}
			}
			if ($row[1] <= $midnight_tonight)
				break;
			$day_num++;
			$t = $midnight = $midnight_tonight;
		}
	}
}
if ($debug_flag)
{
	echo '<p>DEBUG: Array of month day data:<p><pre>'.PHP_EOL;
	for ($i = 1; $i <= $days_in_month; $i++)
	{
		if (isset($d[$i]["id"]))
		{
			$n = count($d[$i]["id"]);
			echo 'Day '.$i.' has '.$n.' entries:'.PHP_EOL;
			for ($j = 0; $j < $n; $j++)
				echo "  ID: " . $d[$i]["id"][$j] .
			" Data: " . $d[$i]["data"][$j] . "\n";
		}
	}
	echo '</pre>'.PHP_EOL;
}
$weekcol = 0;
echo '<table class="table-bordered">'.PHP_EOL;
$sql = "SELECT room_name, capacity, id, description, statut_room FROM ".TABLE_PREFIX."_room WHERE area_id=$area ORDER BY order_display,room_name";
$res = grr_sql_query($sql);
echo "<tr><th></th>\n";
//$t2 = mktime(0, 0, 0, $month, 1, $year);
for ($k = 1; $k <= $days_in_month; $k++)
{
    $t2 = mktime(0, 0, 0, $month, $k, $year);
	$cday = date("j", $t2);
	$cweek = date("w", $t2);
	$name_day = ucfirst(utf8_strftime("%a %d", $t2));
	$temp = mktime(0, 0, 0, $month,$cday,$year);
	$jour_cycle = grr_sql_query1("SELECT Jours FROM ".TABLE_PREFIX."_calendrier_jours_cycle WHERE DAY='$temp'");

	//$t2 += 86400;

	if ($display_day[$cweek] == 1)
	{
        if (isHoliday($temp)) {echo tdcell("cell_hours_ferie");}
        else if (isSchoolHoliday($temp)) {echo tdcell("cell_hours_vacance");}
        else {echo tdcell("cell_hours");}
        echo $name_day;
        if (Settings::get("jours_cycles_actif") == "Oui" && intval($jour_cycle) > -1)
        {
            if (intval($jour_cycle) > 0)
                echo "<br /></><i> ".ucfirst(substr(get_vocab("rep_type_6"), 0, 1)).$jour_cycle."</i>";
            else
            {
                if (strlen($jour_cycle) > 5)
                    $jour_cycle = substr($jour_cycle, 0, 3)."..";
                echo "<br /></><i>".$jour_cycle."</i>";
            }
        }
        echo "</td>";
	}
}
echo "</tr>";
// répète la suite des jours pour une meilleure visibilité dans les domaines avec beaucoup de ressources
echo "<tfoot><tr><th></th>\n";
//$t2 = mktime(0, 0, 0, $month, 1, $year);
for ($k = 1; $k <= $days_in_month; $k++)
{
    $t2 = mktime(0, 0, 0, $month, $k, $year);
	$cday = date("j", $t2);
	$cweek = date("w", $t2);
	$name_day = ucfirst(utf8_strftime("%a %d", $t2));
	$temp = mktime(0, 0, 0, $month,$cday,$year);
	$jour_cycle = grr_sql_query1("SELECT Jours FROM ".TABLE_PREFIX."_calendrier_jours_cycle WHERE DAY='$temp'");
    if ($display_day[$cweek] == 1)
	{
        if (isHoliday($temp)) {echo tdcell("cell_hours_ferie");}
        else if (isSchoolHoliday($temp)) {echo tdcell("cell_hours_vacance");}
        else {echo tdcell("cell_hours");}
        echo $name_day;
        if (Settings::get("jours_cycles_actif") == "Oui" && intval($jour_cycle) > -1)
        {
            if (intval($jour_cycle) > 0)
                echo "<br /></><i> ".ucfirst(substr(get_vocab("rep_type_6"), 0, 1)).$jour_cycle."</i>";
            else
            {
                if (strlen($jour_cycle) > 5)
                    $jour_cycle = substr($jour_cycle, 0, 3)."..";
                echo "<br /></><i>".$jour_cycle."</i>";
            }
        }
        echo "</td>";
	}
}
echo "</tr></tfoot>";
$li = 0;
for ($ir = 0; ($row = grr_sql_row($res, $ir)); $ir++)
{
	$verif_acces_ressource = verif_acces_ressource(getUserName(), $row[2]);
	if ($verif_acces_ressource)
	{
		$acces_fiche_reservation = verif_acces_fiche_reservation(getUserName(), $row[2]);
		echo "<tr><th class=\"tableau_month_all2\">" . htmlspecialchars($row[0]) ."</th>\n";
		$li++;
		//$t2 = mktime(0, 0, 0,$month, 1, $year);
		for ($k = 1; $k <= $days_in_month; $k++)
		{
            $t2 = mktime(0, 0, 0,$month, $k, $year);
			$cday = date("j", $t2);
			$cweek = date("w", $t2);
			//$t2 += 86400;
			if ($display_day[$cweek] == 1)
			{
				echo "<td class=\"cell_month\"> ";
				if (est_hors_reservation(mktime(0, 0, 0, $month, $cday, $year), $area))
				{
					echo "<div class=\"empty_cell\">";
					echo "<img src=\"img_grr/stop.png\" alt=\"".get_vocab("reservation_impossible")."\"  title=\"".get_vocab("reservation_impossible")."\" width=\"16\" height=\"16\" class=\"".$class_image."\"  /></div>\n";
				}
				else
				{
					if (isset($d[$cday]["id"][0])) // il y a une réservation au moins à afficher
					{
						$n = count($d[$cday]["id"]);
						for ($i = 0; $i < $n; $i++)
						{
							if ($i == 11 && $n > 12)
							{
								echo " ...\n";
								break;
							}
							for ($i = 0; $i < $n; $i++)
							{
								if ($d[$cday]["room"][$i] == $row[0])
								{
									echo "<table class='table-bordered'><tr>";
									tdcell($d[$cday]["color"][$i]);
                                    echo "<span class=\"small_planning\">";
									if ($d[$cday]["res"][$i] != '-')
										echo " <img src=\"img_grr/buzy.png\" alt=\"".get_vocab("ressource actuellement empruntee")."\" title=\"".get_vocab("ressource actuellement empruntee")."\" width=\"20\" height=\"20\" class=\"image\" /> \n";
									if ((isset($d[$cday]["option_reser"][$i])) && ($d[$cday]["option_reser"][$i] != -1))
										echo " <img src=\"img_grr/small_flag.png\" alt=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")."\" title=\"".get_vocab("reservation_a_confirmer_au_plus_tard_le")." ".time_date_string_jma($d[$cday]["option_reser"][$i],$dformat)."\" width=\"20\" height=\"20\" class=\"image\" /> \n";
									if ((isset($d[$cday]["moderation"][$i])) && ($d[$cday]["moderation"][$i] == 1))
										echo " <img src=\"img_grr/flag_moderation.png\" alt=\"".get_vocab("en_attente_moderation")."\" title=\"".get_vocab("en_attente_moderation")."\" class=\"image\" /> \n";
									
									if ($acces_fiche_reservation)
									{
										if (Settings::get("display_level_view_entry") == 0)
										{
											$currentPage = 'month_all2';
											$id =   $d[$cday]["id"][$i];
											echo "<a title=\"".htmlspecialchars($d[$cday]["who1"][$i])."\" data-width=\"675\" onclick=\"request($id,$cday,$month,$year,'all','$currentPage',readData);\" data-rel=\"popup_name\" class=\"poplight\">" .$d[$cday]["who1"][$i]."</a>";
										}
										else
										{
											echo "<a title=\"".htmlspecialchars($d[$cday]["data"][$i])."\" href=\"view_entry.php?id=" . $d[$cday]["id"][$i]."&amp;page=month_all2\">"
											.$d[$cday]["who1"][$i]{0}
											. "</a>";
										}
									}
									else
										echo $d[$cday]["who1"][$i]{0};
									echo "</span></td></tr></table>";
								}
							}
						}
					}
                    // la ressource est-elle accessible en réservation ? on affiche le lien vers edit_entry
                    $date_booking = $t2 +86400 ; // le jour courant à minuit
                    $hour =  date("H",$date_now); // l'heure courante, par défaut
                    if ((($authGetUserLevel > 1) || (auth_visiteur(getUserName(), $row['2']) == 1)) && (UserRoomMaxBooking(getUserName(), $row['2'], 1) != 0) && verif_booking_date(getUserName(), -1, $row['2'], $date_booking, $date_now, $enable_periods) && verif_delais_max_resa_room(getUserName(), $row['2'], $date_booking) && verif_delais_min_resa_room(getUserName(), $row['2'], $date_booking) && plages_libre_semaine_ressource($row['2'], $month, $cday, $year) && (($row['4'] == "1") || (($row['4'] == "0") && (authGetUserLevel(getUserName(),$row['2']) > 2) )) && $_GET['pview'] != 1)
					{
						if ($enable_periods == 'y')
							echo '<a href="edit_entry.php?room=',$row["2"],'&amp;period=&amp;year=',$year,'&amp;month=',$month,'&amp;day=',$cday,'&amp;page=month_all2" title="',get_vocab("cliquez_pour_effectuer_une_reservation"),'"><span class="glyphicon glyphicon-plus"></span></a>',PHP_EOL;
						else
							echo '<a href="edit_entry.php?room=',$row["2"],'&amp;hour=',$hour,'&amp;minute=0&amp;year=',$year,'&amp;month=',$month,'&amp;day=',$cday,'&amp;page=month_all2" title="',get_vocab("cliquez_pour_effectuer_une_reservation"),'"><span class="glyphicon glyphicon-plus"></span></a>',PHP_EOL;;
					}
				}
				echo "</td>\n";
			}
		}
		echo "</tr>";
	}
}
echo "</table>";
echo  "<div id=\"popup_name\" class=\"popup_block\" ></div>";
if ($_GET['pview'] != 1)
	echo "<div id=\"toTop\"> ^ Haut de la page";
bouton_retour_haut ();
echo " </div>";
echo " </div>";
echo " </div>";
affiche_pop_up(get_vocab("message_records"),"user");
echo "</div>";
include "footer.php";
?>
