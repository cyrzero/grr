<?php
/*
 * Author : Cédric Berthomé
 * Date : 06/2015
 * Gère l'enregisrement de fichier sur le serveur et référencement dans la base de donnée.
 * 
 */ 
include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/mincals.inc.php";
include "include/mrbs_sql.inc.php";

//vérifie si des fichiers ont bien été transmis
if (isset ($_FILES) && is_array($_FILES)){
	//nombre de fichiers envoyés
	$nb = count($_FILES["myFiles"]["name"]);
	//si une id à bien été reçue
	if(isset ($_POST["id_entry"])){
		$id = $_POST["id_entry"];
		$file = $_POST["selectedfile"];
	}
	else{
		//~ echo "Aucun id reçue !";
		$id = $_GET['id'];
		$file = $_GET['selectedfile'];
	}
	//chemin de destination
	$uploadDir = realpath(".")."/uploadedFiles/";
	//echo $uploadDir ;
	if ($nb > 0) {
		//echo "<br>received 1 or more files";
		for($i=0; $i<$nb ; $i++){
			echo "<p> Fichier : ".$_FILES["myFiles"]["name"][$i];
			echo "<br>Taille : ".$_FILES["myFiles"]["size"][$i];
			//Enregistre les fichiers sur le répertoire de destination et sa référence dans la bdd.
			$copie = move_uploaded_file($_FILES["myFiles"]["tmp_name"][$i], $uploadDir.$_FILES["myFiles"]["name"][$i]);
			//prepare le rename du fichier en concaténant l'id_entry de la réservation, un nombre aléatoire et l'extension du fichier.
			$fileExt = pathinfo($uploadDir.$_FILES["myFiles"]["name"][$i], PATHINFO_EXTENSION);
			$fileName = $id."_".mt_rand(0,9999).".".$fileExt;
			if (rename($uploadDir.$_FILES["myFiles"]["name"][$i], $uploadDir.$fileName)){
				//ajout dans la base de donnée.
				$req = "INSERT INTO ".TABLE_PREFIX."_files (id_entry, file_name, public_name) VALUES ('".$id."', '".$fileName."', '".$_FILES["myFiles"]["name"][$i]."')";
				if (grr_sql_command($req) < 0){
					echo "<br>erreur d'enregistrement sur base de donnée";
				}
				else{
					if ($copie){
						echo "<br> <span style='color:green'>Fichier enregistré</span></p>";
						header('Location: week_all.php?');
					}
					else{
						echo "<br><span style='color:red'>Erreur d'enregistrement</span></p>";
					}
				}
			}
			else{
				echo "<br><span style='color:red'>Erreur, le fichier n'a pu être renommé</span></p>";
			}
			
		}
	}
	else{
		echo "<br><span style='color:red'>Erreur, aucun fichier envoyé</span></p>";
	}
}
else{
	echo "<br><span style='color:red'>Erreur, Aucune donnée reçue</span></p>";
}

?>
