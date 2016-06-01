<?php
/*
 * Author : Cédric Berthomé
 * Date : 06/2015
 * Gère le téléchargement de fichiers enregistrés sur le serveur.
 * 
 */ 
include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/mincals.inc.php";
include "include/mrbs_sql.inc.php";

//définit le chemin d'accès du fichier transmit par la demande
$uploadDir = realpath(".")."/uploadedFiles/";
$FileToView = $uploadDir.$_GET["name"];

if (file_exists($FileToView)) {
    // prépare le fichier pour le téléchargement
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($FileToView));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($FileToView));
    //prépare les données pour éviter une corruption puis lance le téléchargement.
    ob_get_clean();
    readfile($FileToView);
    ob_end_flush();
    exit;
}

?>
