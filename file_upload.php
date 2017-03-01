 <head>
<meta charset="UTF-8">
</head> 
<?php
/*
 * Author : Cédric Berthomé
 * Date : 06/2015
 * Interface pour l'upload de fichiers sur le serveur.
 * 
 */ 
include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/mincals.inc.php";
include "include/mrbs_sql.inc.php";

require_once("./include/settings.class.php");
if (!Settings::load())
	die("Erreur chargement settings");
require_once("./include/session.inc.php");
include "include/resume_session.php";
include "include/language.inc.php";
?>
<script type="text/javascript" src="js/functions.js"></script>
<link rel="stylesheet" href="include/upload.css" type="text/css">

<?php
print_header($day, $month, $year, $type_session);
//ajout fichiers
$id = $_GET['id'];
echo '
<div class="upload">
	
	<form id="uploadForm" method="post" action="upload.php" enctype="multipart/form-data">
	<p>Choisissez un ou plusieurs fichiers à enregistrer : <br></p>
	<input type = "file" multiple name="myFiles[]" accept="image/*, text/*" id="myFiles"><br>
	<input type = "hidden" id = "id_entry" value = "'.$id.'">
	<input type="submit" value="Envoyer" id ="btnValidUpload">
	</form>
	
	<progress id="avancement" value="0" max = "100" aria-valuemin="0" aria-valuemax="100"></progress>
	
	<output id=infos> </output>
	
	<form id="uploadForm" method="post" action="day.php" enctype="multipart/form-data">

	<input type="submit" value="Retour" id ="btnRetourUpload">
	
	</form>
</div>
';

?>

<script type="text/javascript" >
	uploadFiles();
</script>
