<?php
/**
 * admin_maj.php
 * interface permettant la mise à jour de la base de données
 * Ce script fait partie de l'application GRR
 * Dernière modification : $Date: 2010-04-07 17:49:56 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: admin_maj.php,v 1.22 2010-04-07 17:49:56 grr Exp $
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
include "../include/connect.inc.php";
include "../include/config.inc.php";	
include "../include/misc.inc.php";
include "../include/functions.inc.php";
include "../include/$dbsys.inc.php";
$grr_script_name = "admin_maj.php";

// Settings
require_once("../include/settings.class.php");
//Chargement des valeurs de la table settingS
if (!Settings::load())
	die("Erreur chargement settings");
// Session related functions
require_once("../include/session.inc.php");
// Paramètres langage
include "../include/language.inc.php";

// définir dans $version_grr numéro de version actuel
//La version 3.5.1 permet dans la BDD grr de modifier la colonne couleur (contenu et type)
//$version_grr = "2.5";
//$version_grr = "3.0";
//~ $version_grr = "3.5";
//~ $version_grr = "3.5.7.8.3";
$version_grr = "3.6";

function traite_requete($requete = "")
{
	mysqli_query($GLOBALS['db_c'], $requete);
	$erreur_no = mysqli_errno($GLOBALS['db_c']);
	if (!$erreur_no)
		$retour = "";
	else
	{
		switch ($erreur_no)
		{
			case "1060":
			// le champ existe déjà : pas de problème
			$retour = "";
			break;
			case "1061":
			// La cléf existe déjà : pas de problème
			$retour = "";
			break;
			case "1062":
			// Présence d'un doublon : création de la cléf impossible
			$retour = "<span style=\"color:#FF0000;\">Erreur (<b>non critique</b>) sur la requête : <i>".$requete."</i> (".mysqli_errno($GLOBALS['db_c'])." : ".mysqli_error($GLOBALS['db_c']).")</span><br />\n";
			break;
			case "1068":
			// Des cléfs existent déjà : pas de problème
			$retour = "";
			break;
			case "1091":
			// Déjà supprimé : pas de problème
			$retour = "";
			break;
			default:
			$retour = "<span style=\"color:#FF0000;\">Erreur sur la requête : <i>".$requete."</i> (".mysqli_errno($GLOBALS['db_c'])." : ".mysqli_error($GLOBALS['db_c']).")</span><br />\n";
			break;
		}
	}
	return $retour;
}
$valid = isset($_POST["valid"]) ? $_POST["valid"] : 'no';
$version_old = isset($_POST["version_old"]) ? $_POST["version_old"] : '';
if (isset($_GET["force_maj"]))
	$version_old = $_GET["force_maj"];
if (isset($_POST['submit']))
{
	if (isset($_POST['login']) && isset($_POST['password']))
	{
		// Test pour tenir compte du changement de nom de la table ".TABLE_PREFIX."_utilisateurs lors du passage à la version 1.8
		$num_version = grr_sql_query1("select NAME from ".TABLE_PREFIX."_setting WHERE NAME='version'");
		if ($num_version != -1)
			$sql = "select upper(login) login, password, prenom, nom, statut from ".TABLE_PREFIX."_utilisateurs where login = '" . $_POST['login'] . "' and password = md5('" . $_POST['password'] . "') and etat != 'inactif' and statut='administrateur' ";
		else
			$sql = "select upper(login) login, password, prenom, nom, statut from utilisateurs where login = '" . $_POST['login'] . "' and password = md5('" . $_POST['password'] . "') and etat != 'inactif' and statut='administrateur' ";
		$res_user = grr_sql_query($sql);
		$num_row = grr_sql_count($res_user);
		if ($num_row == 1)
			$valid = 'yes';
		else
			$message = get_vocab("wrong_pwd");
	}
}
if (Settings::get('sso_statut') == 'lcs')
{
	include LCS_PAGE_AUTH_INC_PHP;
	include LCS_PAGE_LDAP_INC_PHP;
	list ($idpers,$login) = isauth();
	if ($idpers)
	{
		list($user, $groups) = people_get_variables($login, true);
		$lcs_tab_login["nom"] = $user["nom"];
		$lcs_tab_login["email"] = $user["email"];
		$long = strlen($user["fullname"]) - strlen($user["nom"]);
		$lcs_tab_login["fullname"] = substr($user["fullname"], 0, $long) ;
		foreach ($groups as $value)
			$lcs_groups[] = $value["cn"];
		// A ce stade, l'utilisateur est authentifié par LCS
		// Etablir à nouveau la connexion à la base
		if (empty($db_nopersist))
			$db_c = mysql_pconnect($dbHost, $dbUser, $dbPass);
		else
			$db_c = mysql_connect($dbHost, $dbUser, $dbPass);
		if (!$db_c || !mysql_select_db ($dbDb))
		{
			echo "\n<p>\n" . get_vocab('failed_connect_db') . "\n";
			exit;
		}
		if (!(is_eleve($login)))
			$user_ext_authentifie = 'lcs_eleve';
		else
			$user_ext_authentifie = 'lcs_non_eleve';
		$password = '';
		$result = grr_opensession($login,$password,$user_ext_authentifie,$lcs_tab_login,$lcs_groups) ;
	}
}
if ((!@grr_resumeSession()) && $valid!='yes')
{
	?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">
	<HTML>
		<HEAD>
			<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php
			if ($unicode_encoding)
				echo "utf-8";
			else
				echo $charset_html;
			?>">
			<link REL="stylesheet" href="../themes/default/css/style.css" type="text/css">
			<TITLE> GRR </TITLE>
			<LINK REL="SHORTCUT ICON" href="./favicon.ico">
				<script type="text/javascript" src="../js/functions.js" ></script>
			</HEAD>
			<BODY>
				<form action="admin_maj.php" method='post' style="width: 100%; margin-top: 24px; margin-bottom: 48px;">
					<div class="center">
						<h2><?php echo get_vocab("maj_bdd"); ?></h2>

						<?php
						if (isset($message))
							echo("<p><span style=\"color:red;\">" . encode_message_utf8($message) . "</span></p>");
						?>
						<fieldset style="padding-top: 8px; padding-bottom: 8px; width: 40%; margin-left: auto; margin-right: auto;">
							<legend style="font-variant: small-caps;"><?php echo get_vocab("identification"); ?></legend>
							<table style="width: 100%; border: 0;" cellpadding="5" cellspacing="0">
								<tr>
									<td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="login"><?php echo get_vocab("login"); ?></label></td>
									<td style="text-align: center; width: 60%;"><input type="text" id="login" name="login" size="16" /></td>
								</tr>
								<tr>
									<td style="text-align: right; width: 40%; font-variant: small-caps;"><label for="password"><?php echo get_vocab("pwd"); ?></label></td>
									<td style="text-align: center; width: 60%;"><input type="password" id="password" name="password" size="16" /></td>
								</tr>
							</table>
							<input type="submit" name="submit" value="<?php echo get_vocab("submit"); ?>" style="font-variant: small-caps;" />
						</fieldset>
					</div>
				</form>
			</body>
			</html>
			<?php
			die();
		};
		$back = '';
		if (isset($_SERVER['HTTP_REFERER']))
			$back = htmlspecialchars($_SERVER['HTTP_REFERER']);
		if ((authGetUserLevel(getUserName(),-1) < 6) && ($valid != 'yes'))
		{
			showAccessDenied($back);
			exit();
		}
		if ($valid == 'no')
		{
			# print the page header
			print_header("", "", "", $type="with_session");
			// Affichage de la colonne de gauche
			include "admin_col_gauche.php";

		}
		else
		{
			?>
			<!doctype html>
			<html>
				<head>
					<meta http-equiv="content-type" content="text/html; charset=<?php
					if ($unicode_encoding)
						echo "utf-8";
					else
						echo $charset_html;
					?>">

					<link rel="stylesheet" href="../themes/default/css/style.css" type="text/css">
					<link rel="shortcut icon" href="favicon.ico">
						<title> grr </title>
					</head>
					<body>
						<?php
					}

					?>
					<script type="text/javascript" src="../js/functions.js" ></script>
					<?php
					$result = '';
					$result_inter = '';
					if (isset($_POST['maj']) || isset($_GET['force_maj']))
					{
						// On commence la mise à jour
					
						if ($version_old < "2.2.3")
						{
							$result .= "<b>Mise à jour jusqu'à la version 2.2.3 :</b><br />";
							
							$result_inter .= traite_requete("ALTER TABLE ".TABLE_PREFIX."_entry ADD `clef` INT(2) NOT NULL DEFAULT '0' AFTER `jours`;");
							$result_inter .= traite_requete("ALTER TABLE ".TABLE_PREFIX."_entry ADD `courrier` INT(2) NOT NULL DEFAULT '0' AFTER `clef`;");
							$result_inter .= traite_requete("ALTER TABLE ".TABLE_PREFIX."_repeat ADD `courrier` INT(2) NOT NULL DEFAULT '0' AFTER `jours`;");
							
							$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = 1 WHERE NAME = 'nb_calendar';");
							$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = 'item' WHERE NAME = 'area_list_format';");
							
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='default_css'");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('default_css', 'bleu')");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = 'bleu' WHERE NAME = 'default_css';");
							}
							
							$req2 = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='mail_destinataire'");
							if ($req2 == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('mail_destinataire', 'votreemail@adresse.xx')");
							}
							
							$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('allow_pdf', 'y')");
							
							if ($result_inter == '')
								$result .= "<span style=\"color:green;\">Ok !</span><br />";
							else
								$result .= $result_inter;
							$result_inter = '';
						}
						
						//maj version 2.4
						if ($version_old < "2.4")
						{
							$result .= "<b>Mise à jour jusqu'à la version 2.4 :</b><br />";
						
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='grr_mail_port';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('grr_mail_port', '25');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = '25' WHERE NAME = 'grr_mail_port';");
							}
							
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='grr_mail_encrypt';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('grr_mail_encrypt', '');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = '' WHERE NAME = 'grr_mail_encrypt';");
							}
							
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='grr_print_auto';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('grr_print_auto', '1');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = '1' WHERE NAME = 'grr_print_auto';");
							}
						
							if ($result_inter == '')
								$result .= "<span style=\"color:green;\">Ok !</span><br />";
							else
								$result .= $result_inter;
							$result_inter = '';
						}
						//maj version 2.5
						//~ if ($version_old < "2.5")
						//~ {
							//~ $result .= "<b>Mise à jour jusqu'à la version 2.5 :</b><br />";
							//~ $result_inter .= traite_requete("Create table if not exists ".TABLE_PREFIX."files(id int not null, id_entry int, file_name varchar(50), public_name varchar(50),Primary key (id), constraint fk_idEntry foreign key (id_entry) references resatest.grr_entry(id));");
								
							//~ if ($result_inter == '')
								//~ $result .= "<span style=\"color:green;\">Ok !</span><br />";
							//~ else
								//~ $result .= $result_inter;
							//~ $result_inter = '';
						//~ }
						
						//modif_Elodie
						//maj version 3.6
						if ($version_old <= "3.5")
						{
							$result .= "<b>Mise à jour jusqu'à la version 3.5 :</b><br />";
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='grr_mail_port';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('grr_mail_port', '25');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = '25' WHERE NAME = 'grr_mail_port';");
							}
							
							//~ $req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='grr_mail_encrypt';");
							//~ if ($req == -1){
								//~ $result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('grr_mail_encrypt', '');");
							//~ }else{
								//~ $result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = '' WHERE NAME = 'grr_mail_encrypt';");
							//~ }
							
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='grr_print_auto';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('grr_print_auto', '1');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = '1' WHERE NAME = 'grr_print_auto';");
							}
							
							//Rajout du logo sur le site
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='logo';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('logo', 'f68661ceafd7e84ba15ed125a39ff1a2.png');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = 'f68661ceafd7e84ba15ed125a39ff1a2.png' WHERE NAME = 'logo';");
							}
							
							//Modification du champ 'company'
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='company';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('company', 'Mairie de Talmont Saint Hilaire');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = 'Mairie de Talmont Saint Hilaire' WHERE NAME = 'company';");
							}
							
							//Objet du mail d'envoi automatique via la base de données (ajout du champ grr_mail_object dans la BDD)
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='grr_mail_object';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('grr_mail_object', 'Demande de réservation');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = 'Demande de réservation' WHERE NAME = 'grr_mail_object';");
							}
							
							//Changement du type de la colonne couleur de la table grr_type_area plus remplacement des anciennes valeurs avec un update
							$tab_couleur[1] = "#F49AC2";
							$tab_couleur[2] = "#99CCCC";
							$tab_couleur[3] = "#FF9999";
							$tab_couleur[4] = "#95a5a6";
							$tab_couleur[5] = "#C0E0FF";
							$tab_couleur[6] = "#FFCC99";
							$tab_couleur[7] = "#e74c3c";
							$tab_couleur[8] = "#3498db";
							$tab_couleur[9] = "#DDFFDD";
							$tab_couleur[10] = "#34495e";
							$tab_couleur[11] = "#2ecc71";
							$tab_couleur[12] = "#9b59b6";
							$tab_couleur[13] = "#f1c40f";
							$tab_couleur[14] = "#FF00DE";
							$tab_couleur[15] = "#2ecc71";
							$tab_couleur[16] = "#e67e22";
							$tab_couleur[17] = "#bdc3c7";
							$tab_couleur[18] = "#C000FF";
							$tab_couleur[19] = "#FF0000";
							$tab_couleur[20] = "#FFFFFF";
							$tab_couleur[21] = "#A0A000";
							$tab_couleur[22] = "#f39c12";
							$tab_couleur[23] = "#1abc9c";
							$tab_couleur[24] = "#9b59b6";
							$tab_couleur[25] = "#4169E1";
							$tab_couleur[26] = "#6A5ACD";
							$tab_couleur[27] = "#AA5050";
							$tab_couleur[28] = "#FFBB20";

							$result_inter .= traite_requete("ALTER TABLE ".TABLE_PREFIX."_type_area MODIFY couleur CHAR(7)");
							
							for($i=1; $i<29; $i++) {
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_type_area SET couleur = '".$tab_couleur[$i]."' WHERE couleur = '".$i."'");
							}

							if ($result_inter == '')
								$result .= "<span style=\"color:green;\">Ok !</span><br />";
							else
								$result .= $result_inter;
							$result_inter = '';
						}
						
						if ($version_old <= "3.6"){
							
							$result .= "<b>Mise à jour jusqu'à la version 3.6 :</b><br />";

							$result_inter .= traite_requete("Create table if not exists ".TABLE_PREFIX."_files(id int not null auto_increment, id_entry int, file_name varchar(50), public_name varchar(50), Primary key (id), constraint fk_idEntry foreign key (id_entry) references ".TABLE_PREFIX."_entry(id));");

							$result_inter .= traite_requete("ALTER TABLE ".TABLE_PREFIX."_overload MODIFY fieldname VARCHAR(40)");
							
							$result_inter .= traite_requete("ALTER TABLE ".TABLE_PREFIX."_overload MODIFY fieldtype VARCHAR(40)");
							
							$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='files';");
							if ($req == -1){
								$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('files', '');");
							}else{
								$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE = '' WHERE NAME = 'files';");
							}	
													
							if ($result_inter == '')
								$result .= "<span style=\"color:green;\">Ok !</span><br />";
							else
								$result .= $result_inter;
							$result_inter = '';
						}
						// Vérification du format des champs additionnels
						// Avant version 1.9.4, les champs add étaient stockés sous la forme <id_champ>champ_encode_en_base_64</id_champ>
						// A partir de la version 1.9.4, les champs add. sont stockés sous la forme @id_champ@url_encode(champ)@/id_champ@
						if (($version_old < "1.9.4") && (Settings::get("maj194_champs_additionnels") != 1) && isset($_POST['maj']))
						{
	  					// On constuite un tableau des id des ".TABLE_PREFIX."overload:
							$sql_overload = grr_sql_query("SELECT id FROM ".TABLE_PREFIX."_overload");
							for ($i = 0; ($row = grr_sql_row($sql_overload, $i)); $i++)
								$tab_id_overload[] = $row[0];
	  						// On selectionne les entrées
							$sql_entry = grr_sql_query("SELECT overload_desc, id FROM ".TABLE_PREFIX."_entry WHERE overload_desc != ''");
							for ($i = 0; ($row = grr_sql_row($sql_entry, $i)); $i++)
							{
								$nouvelle_chaine = "";
								foreach ($tab_id_overload as $value)
								{
									$begin_string = "<".$value.">";
									$end_string = "</".$value.">";
									$begin_pos = strpos($row[0],$begin_string);
									$end_pos = strpos($row[0],$end_string);
									if ( $begin_pos !== false && $end_pos !== false )
									{
										$first = $begin_pos + strlen($begin_string);
										$data = substr($row[0],$first,$end_pos-$first);
										$data  = urlencode(base64_decode($data));
										$nouvelle_chaine .= "@".$value."@".$data."@/".$value."@";
									}
								}
		  						// On met à jour le champ
								if ($nouvelle_chaine != '')
									$up = grr_sql_query("UPDATE ".TABLE_PREFIX."_entry set overload_desc = '".$nouvelle_chaine."' where id='".$row[1]."'");
							}
	  						// on inscrit le résultat dans la table ".TABLE_PREFIX."_settings
							grr_sql_query("DELETE from ".TABLE_PREFIX."_setting where NAME = 'maj194_champs_additionnels'");
							grr_sql_query("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('maj194_champs_additionnels', '1');");
							$result .= "<b>Mise à jour des champs additionnels : </b><span style=\"color:green;\">Ok !</span><br /><br />";
						}
						// Mise à jour du champ "qui_peut_reserver_pour
						// La version 1.9.6 a introduit un niveau supplémentaire pour le champ qui_peut_reserver_pour, ce qui oblige à un décalage : les niveaux 5 deviennent des niveaux 6
						if (($version_old < "1.9.6") && (Settings::get("maj196_qui_peut_reserver_pour") != 1) && (isset($_POST['maj']) ))
						{
	   						// On met à jour le champ
							$up = grr_sql_query("UPDATE ".TABLE_PREFIX."_room set qui_peut_reserver_pour='6' where qui_peut_reserver_pour='5'");
							grr_sql_query("DELETE from ".TABLE_PREFIX."_setting where NAME = 'maj196_qui_peut_reserver_pour'");
							grr_sql_query("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('maj196_qui_peut_reserver_pour', '1');");
							$result .= "<b>Mise à jour du champs qui_peut_reserver_pour : </b><span style=\"color:green;\">Ok !</span><br /><br />";
						}
						// Mise à jour du numéro de version
						$req = grr_sql_query1("SELECT VALUE FROM ".TABLE_PREFIX."_setting WHERE NAME='version'");
						if ($req == -1)
							$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('version', '".$version_grr."');");
						else
							$result_inter .= traite_requete("UPDATE ".TABLE_PREFIX."_setting SET VALUE='".$version_grr."' WHERE NAME='version';");
						// Mise à jour du numéro de RC
						$req = grr_sql_command("DELETE FROM ".TABLE_PREFIX."_setting WHERE NAME='versionRC'");
						$result_inter .= traite_requete("INSERT INTO ".TABLE_PREFIX."_setting VALUES ('versionRC', '".$version_grr_RC."');");
						//Re-Chargement des valeurs de la table settingS
						if (!Settings::load())
							die("Erreur chargement settings");
						affiche_pop_up(get_vocab("maj_good"),"force");
					}
					// Numéro de version effective
					$version_old = Settings::get("version");
					if ($version_old == "")
						$version_old = "1.3";
					// Numéro de RC
					$version_old_RC = Settings::get("versionRC");
					// Calcul du numéro de version actuel de la base qui sert aux test de comparaison et de la chaine à afficher
					if ($version_old_RC == "")
					{
						$version_old_RC = 9;
						$display_version_old = $version_old;
					}
					else
						$display_version_old = $version_old."_RC".$version_old_RC;
					$version_old .= ".".$version_old_RC;
					// Calcul de la chaine à afficher
					if ($version_grr_RC == "")
						$display_version_grr = $version_grr.$sous_version_grr;
					else
						$display_version_grr = $version_grr."_RC".$version_grr_RC;
						
					/* 			  AFFICHAGE
					 * 				 DE
					 * 				 LA
					 * 			   	PAGE
					 *    Numéro de version et mise à jour (la page bleue)
					 * (après avoir efféctué les mises à jours)
					 */
					 
					echo "<h2>".get_vocab('admin_maj.php')."</h2>";
					echo "<hr />";
					// Numéro de version
					//Hugo - Mise a jour temporaire du numéro de version à afficher
					//11/06/2013
					$display_version_grr = Settings::get("version");
					//$display_version_grr = $version_grr.$sous_version_grr." RC".$version_grr_RC;
					echo "<h3>".get_vocab("num_version_title")."</h3>\n";
					echo "<p>".get_vocab("num_version").$display_version_grr;
					echo "</p>\n";
					echo get_vocab('database') . grr_sql_version() . "\n";
					echo "<br />" . get_vocab('system') . php_uname() . "\n";
					echo "<br />Version PHP : " . phpversion() . "\n";
					//Hugo - Mise a jour temporaire du lien à afficher
					//11/06/2013
					$grr_devel_url = "http://sourceforge.net/projects/grrv2/";
					echo "<p>".get_vocab("maj_go_www")."<a href=\"".$grr_devel_url."\">".get_vocab("mrbs")."</a></p>\n";
					echo "<hr />\n";
					// Mise à jour de la base de donnée
					echo "<h3>".get_vocab("maj_bdd")."</h3>";
					// Vérification du numéro de version
					//On rentre dans la boucle si la version actuelle est antérieure à la version souhaitée
					if (verif_version())
					{
						echo "<form action=\"admin_maj.php\" method=\"post\">";
						echo "<p><span style=\"color:red;\"><b>".get_vocab("maj_bdd_not_update");
						echo " ".get_vocab("maj_version_bdd").$display_version_old;
						echo "</b></span><br />";
						echo get_vocab("maj_do_update")."<b>".$display_version_grr."</b></p>";
						echo "<input type=\"submit\" value=\"".get_vocab("maj_submit_update")."\" />";
						echo "<input type=\"hidden\" name=\"maj\" value=\"yes\" />";
						echo "<input type=\"hidden\" name=\"version_old\" value=\"$version_old\" />";
						echo "<input type=\"hidden\" name=\"valid\" value=\"$valid\" />";
						echo "</form>";
					}
					else
					{
						echo "<p>".get_vocab("maj_no_update_to_do")."</p>";
						echo "<p style=\"text-align:center;\"><a href=\"../week_all.php\">".get_vocab("welcome")."</a></p>";
					}
					echo "<hr />";
					if (isset($result) && ($result != ''))
					{
						echo "<div class=\"page_sans_col_gauche\">";
						echo "<h2>".encode_message_utf8("Résultat de la mise à jour")."</h2>";
						echo encode_message_utf8($result);
						echo $result_inter;
						echo "</div>";
					}
					// Test de cohérence des types de réservation
					if ($version_grr > "1.9.1")
					{
						$res = grr_sql_query("SELECT DISTINCT type FROM ".TABLE_PREFIX."_entry ORDER BY type");
						if ($res)
						{
							$liste = "";
							for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
							{
								$test = grr_sql_query1("SELECT type_letter FROM ".TABLE_PREFIX."_type_area WHERE type_letter='".$row[0]."'");
								if ($test == -1) $liste .= $row[0]." ";
							}
							if ($liste != "")
							{
								echo encode_message_utf8("<table border=\"1\" cellpadding=\"5\"><tr><td><p><span style=\"color:red;\"><b>ATTENTION : votre table des types de réservation n'est pas à jour :</b></span></p>");
								echo encode_message_utf8("<p>Depuis la version 1.9.2, les types de réservation ne sont plus définis dans le fichier config.inc.php
									mais directement en ligne. Un ou plusieurs types sont actuellement utilisés dans les réservations
									mais ne figurent pas dans la tables des types. Cela risque d'engendrer des messages d'erreur. <b>Il s'agit du ou des types suivants : ".$liste."</b>");
								echo encode_message_utf8("<br /><br />Vous devez donc définir dans <a href= './admin_type.php'>l'interface de gestion des types</a>, le ou les types manquants, en vous aidant éventuellement des informations figurant dans votre ancien fichier config.inc.php.</p></td></tr></table>");
							}
						}
					}
					// fin de l'affichage de la colonne de droite
					if ($valid == 'no')
						echo "</td></tr></table>";
					?>
				</body>
				</html>
