<?php
/**
 * admin_type_modify.php
 * interface de création/modification des types de réservations
 * Ce script fait partie de l'application GRR
 * Dernière modification : $Date: 2010-03-03 14:41:34 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: admin_type_modify.php,v 1.8 2010-03-03 14:41:34 grr Exp $
 * @filesource
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
include "include/admin.inc.php";
$grr_script_name = "admin_type_modify.php";
$ok = NULL;
$back = '';
if (isset($_SERVER['HTTP_REFERER']))
	$back = htmlspecialchars($_SERVER['HTTP_REFERER']);
$day   = date("d");
$month = date("m");
$year  = date("Y");
check_access(6, $back);
// Initialisation
$id_type = isset($_GET["id_type"]) ? $_GET["id_type"] : 0;
$type_name = isset($_GET["type_name"]) ? $_GET["type_name"] : NULL;
$order_display = isset($_GET["order_display"]) ? $_GET["order_display"] : NULL;
$type_letter = isset($_GET["type_letter"]) ? $_GET["type_letter"] : NULL;
$couleur = isset($_GET["couleur"]) ? $_GET["couleur"] : NULL;
$disponible = isset($_GET["disponible"]) ? $_GET["disponible"] : NULL;
$msg = '';
if (isset($_GET["change_room_and_back"]))
{
	$_GET['change_type'] = "yes";
	$_GET['change_done'] = "yes";
}
// Enregistrement
if (isset($_GET['change_type']))
{
	$_SESSION['displ_msg'] = "yes";
	if ($type_name == '')
		$type_name = "A définir";
	if ($type_letter == '')
		$type_letter = "A";
	if ($couleur == '')
		$couleur = "1";
	if ($disponible == '')
		$disponible = "2";
	if ($id_type > 0)
	{
		// Test sur $type_letter
		$test = grr_sql_query1("SELECT count(id) FROM ".TABLE_PREFIX."_type_area WHERE type_letter='".$type_letter."' AND id!='".$id_type."'");
		if ($test > 0)
			$msg = "Enregistrement impossible : Un type portant la même lettre existe déjà.";
		else
		{
			$sql = "UPDATE ".TABLE_PREFIX."_type_area SET
			type_name='".protect_data_sql($type_name)."',
			order_display =";
			if (is_numeric($order_display))
				$sql= $sql .intval($order_display).",";
			else
				$sql= $sql ."0,";
			$sql = $sql . 'type_letter="'.$type_letter.'",';
			$sql = $sql . 'couleur="'.$couleur.'",';
			$sql = $sql . 'disponible="'.$disponible.'"';
			$sql = $sql . " WHERE id=$id_type";
			if (grr_sql_command($sql) < 0)
			{
				fatal_error(0, get_vocab('update_type_failed') . grr_sql_error());
				$ok = 'no';
			}
			else
				$msg = get_vocab("message_records");
		}
	}
	else
	{
		// Test sur $type_letter
		$test = grr_sql_query1("SELECT count(id) FROM ".TABLE_PREFIX."_type_area WHERE type_letter='".$type_letter."'");
		if ($test > 0)
			$msg = "Enregistrement impossible : Un type portant la même lettre existe déjà.";
		else
		{
			$sql = "INSERT INTO ".TABLE_PREFIX."_type_area SET
			type_name='".protect_data_sql($type_name)."',
			order_display =";
			if (is_numeric($order_display))
				$sql= $sql .intval($order_display).",";
			else
				$sql= $sql ."0,";
			$sql = $sql . 'type_letter="'.$type_letter.'",';
			$sql = $sql . 'couleur="'.$couleur.'"';
			if (grr_sql_command($sql) < 0)
			{
				fatal_error(1, "<p>" . grr_sql_error());
				$ok = 'no';
			}
			else
				$msg = get_vocab("message_records");
		}

	}
}
// Si pas de problème, retour à la page d'accueil après enregistrement
if ((isset($_GET['change_done'])) && (!isset($ok)))
{
	$_SESSION['displ_msg'] = 'yes';
	Header("Location: "."admin_type.php?msg=".$msg);
	exit();
}
# print the page header
print_header("", "", "", $type="with_session");
include "admin_col_gauche.php";
echo "<div class=\"page_sans_col_gauche\">";
affiche_pop_up($msg,"admin");
if ((isset($id_type)) && ($id_type > 0))
{
	$res = grr_sql_query("SELECT * FROM ".TABLE_PREFIX."_type_area WHERE id=$id_type");
	if (!$res)
		fatal_error(0, get_vocab('message_records_error'));
	$row = grr_sql_row_keyed($res, 0);
	grr_sql_free($res);
	$change_type = 'modif';
	echo "<h2>".get_vocab("admin_type_modify_modify.php")."</h2>";
}
else
{
	$row["id"] = '0';
	$row["type_name"] = '';
	$row["type_letter"] = '';
	$row["order_display"]  = 0;
	$row["disponible"]  = 2;
	$row["couleur"]  = '';
	echo "<h2>".get_vocab('admin_type_modify_create.php')."</h2>";
}
echo get_vocab('admin_type_explications')."<br /><br />";
?>
<form action="admin_type_modify.php" method='get'>
	<?php
	echo '<div>',PHP_EOL,'<input type="hidden" name="id_type" value="',$id_type,'" /></div>',PHP_EOL;

	echo '<table class="table table-bordered">',PHP_EOL;
	echo '<tr>',PHP_EOL;
	echo '<td>',get_vocab("type_name"),get_vocab("deux_points"),'</td>',PHP_EOL;
	echo '<td>',PHP_EOL,'<input class="form-control" type="text" name="type_name" value="',htmlspecialchars($row["type_name"]),'" size="20" />',PHP_EOL,'</td>',PHP_EOL;
	echo '</tr>',PHP_EOL,'<tr>',PHP_EOL;
	echo '<td>',get_vocab("type_num"),get_vocab("deux_points"),'</td>',PHP_EOL;
	echo '<td>',PHP_EOL;
	echo '<select class="form-control" name="type_letter" size="1">',PHP_EOL;
	echo '<option value="">',get_vocab("choose"),'</option>',PHP_EOL;
	$letter = "A";
	for ($i = 1; $i <= 100; $i++)
	{
		echo '<option value="',$letter,'" ';
		if ($row['type_letter'] == $letter)
			echo ' selected="selected"';
		echo '>',$letter,'</option>',PHP_EOL;
		$letter++;
	}
	echo '</select>',PHP_EOL;
	echo '</td>',PHP_EOL;
	echo '</tr>',PHP_EOL,'<tr>',PHP_EOL;
	echo '<td>',get_vocab("type_order"),get_vocab("deux_points"),'</td>',PHP_EOL;
	echo '<td>',PHP_EOL,'<input class="form-control" type="text" name="order_display" value="',htmlspecialchars($row["order_display"]),'" size="20" /></td>',PHP_EOL;
	echo '</tr>',PHP_EOL;
	echo '<tr>',PHP_EOL,'<td>',get_vocab("disponible_pour"),get_vocab("deux_points"),'</td>',PHP_EOL;
	echo '<td>',PHP_EOL,'<select class="form-control" name="disponible" size="1">',PHP_EOL;
	echo '<option value="2"';
	if ($row['disponible'] == '2')
		echo ' selected="selected"';
	echo '>',get_vocab("all"),'</option>',PHP_EOL;
	echo '<option value="3"';
	if ($row['disponible'] == '3')
		echo ' selected="selected"';
	echo '>',get_vocab("gestionnaires_et_administrateurs"),'</option>',PHP_EOL;
	echo '<option value="5"';
	if ($row['disponible'] == '5')
		echo ' selected="selected"';
	echo '>',get_vocab("only_administrators"),'</option>',PHP_EOL;
	echo '</select>',PHP_EOL;
	echo '</td>',PHP_EOL,'</tr>',PHP_EOL;
	if ($row["couleur"] != '')
	{
		echo '<tr>',PHP_EOL;
		echo '<td>',get_vocab("type_color"),get_vocab("deux_points"),'</td>',PHP_EOL;
		echo '<td bgcolor="',$tab_couleur[$row["couleur"]],'"> </td>',PHP_EOL;
		echo '</tr>',PHP_EOL;
	}
	echo '</table>',PHP_EOL;
	echo '<p>',get_vocab("type_color"),get_vocab("deux_points"),'</p>',PHP_EOL;
	echo '<table class="table table-bordered">',PHP_EOL;
	echo '<tr>',PHP_EOL;
	$nct = 0;
	foreach ($tab_couleur as $key=>$value)
	{
		$checked = " ";
		if ($key == $row["couleur"])
			$checked = 'checked="checked"';
		if (++$nct > 4)
		{
			$nct = 1;
			echo '</tr>',PHP_EOL,'<tr>';
		}
		echo '<td style="background-color:',$tab_couleur[$key],';">',PHP_EOL,'<input type="radio" name="couleur" value="',$key,'" ',$checked,' />______________</td>',PHP_EOL;
	}
	echo '</tr>',PHP_EOL,'</table>',PHP_EOL;
	echo '<table>',PHP_EOL,'<tr>',PHP_EOL,'<td>',PHP_EOL;
	echo '<input class="btn btn-primary" type="submit" name="change_type"  value="',get_vocab("save"),'" />',PHP_EOL;
	echo '</td>',PHP_EOL,'<td>',PHP_EOL;
	echo '<input class="btn btn-primary" type="submit" name="change_done" value="',get_vocab("back"),'" />',PHP_EOL;
	echo '</td>',PHP_EOL,'<td>',PHP_EOL;
	echo '<input class="btn btn-primary" type="submit" name="change_room_and_back" value="',get_vocab("save_and_back"),'" />',PHP_EOL;
	echo '</td>',PHP_EOL,'</tr>',PHP_EOL,'</table>',PHP_EOL;
	?>
</form>
</div>
</body>
</html>
