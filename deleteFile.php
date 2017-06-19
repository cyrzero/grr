<?php
/*
 * Author : Cédric Berthomé
 * Date : 06/2015
 * Gère la suppression de fichiers enregistrés sur le serveur.
 *
 */

include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/mincals.inc.php";
include "include/mrbs_sql.inc.php";

 if (isset($_POST["idFile"]) && isset($_POST["id"])){
	$idFile = $_POST["idFile"];
	$id = $_POST["id"];

  $sql = "SELECT file_name FROM ".TABLE_PREFIX."_files where id = '".$idFile."'";
  $res = grr_sql_query($sql);
  $name = mysqli_fetch_row($res);
	// prépare chemin du fichier à effacer
	$uploadDir = realpath(".")."/uploadedFiles/";
	$toDelFile = $uploadDir.$name[0];
	//prépare la requête de suppression
	$delReq = 'delete FROM '.TABLE_PREFIX.'_files where id_entry = '.$id.' and file_name = "'.$name[0].'"';
	//vérifie si le fichier existe
	if (file_exists($toDelFile)){
			// efface le fichier du serveur
			if (unlink($toDelFile)){
				if (grr_sql_command($delReq) < 0){
					echo "<span style='color:red'>erreur de suppression dans la base de donnée</span>";
					abort();
				}
				else{
					echo "<span style='color:green'>Fichier supprimé</span>";
				}
			}
			else{
				echo "<span style='color:red'>erreur, le fichier n'a pu être supprimé</span>";
				abort();
			}
	}
	else{
		echo "<span style='color:orange'>le fichier n'existe pas, maj de la base de donnée.</span><br>";
		// fichier n'existe pas, efface sa référence de la base de donnée.
		if (grr_sql_command($delReq) < 0){
			echo "<span style='color:red'>erreur de suppression dans la base de donnée</span>";
			abort();
		}
		else{
			echo "<span style='color:green'>La base de donnée à été corrigée avec succès</span>";
		}
	}
}
else {
	echo "<span style='color:red'>Erreur, aucune donnée reçue.</span>";
}
?>
