<?php
/*-----MAJ Loïs THOMAS  --> Page de traitement du formulaire contact.php -----*/
include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/$dbsys.inc.php";
include "include/mrbs_sql.inc.php";
include "phpmailer/class.phpmailer.php";

include "../language/lang.fr";
	
$grr_script_name = "week_all.php";
// Settings
require_once("./include/settings.class.php");
if (!Settings::load())
	die("Erreur chargement settings");
$msg_erreur = "Erreur. Les champs suivants doivent être obligatoirement remplis :<br/><br/>";
$msg_ok = "Votre demande a bien été prise en compte.";
$message = $msg_erreur;
define('MAIL_DESTINATAIRE','informatique@talmontsainthilaire.fr');
define('MAIL_SUJET','GRR : Demande de réservation ');

if (empty($_POST['nom']))
	$message .= "Votre nom";
if (empty($_POST['prenom']))
	$message .= "Votre prénom<br/>";
if (empty($_POST['email']))
	$message .= "Votre adresse email<br/>";
if (empty($_POST['subject']))
	$message .= "Le sujet de votre demande<br/>";
if (empty($_POST['area']))
	$message .= "Le domaine n'est pas rempli<br/>";
if (empty($_POST['room']))
	$message .= "Aucune salle de choisie<br/>";
if (empty($_POST['jours']))
	$message .= "Aucune jours choisi <br/>";
if (empty($_POST['mois']))
	$message .= "Aucune mois choisi <br/>";
if (empty($_POST['année']))
	$message .= "Aucune année choisie <br/>";
if (empty($_POST['duree']))
	$message .= "Aucune durée choisie <br/>";
if (empty($_POST['start_']))
	$message .= "Aucune heure chosie <br/>";
if (empty($_POST['end_']))
	$message .= "Aucune durée choisie <br/>";
foreach ($_POST as $index => $valeur)
	$index = stripslashes(trim($valeur));

//On va récupérer le nom de domaine en fonction de son id
$id .= $_POST['area'] ;
$sql_areaName = '';
$res_areaName = '';
$sql_areaName .= "SELECT area_name FROM ".TABLE_PREFIX."_area where id = \"$id\" ";
$res_areaName .= grr_sql_query1($sql_areaName);

//On va récupérer le nom de ressource en fonction de son id
$id1 .= $_POST['room'] ;
$sql_roomName = '';
$res_roomName = '';
$sql_roomName .= "SELECT room_name FROM ".TABLE_PREFIX."_room where id = \"$id1\" ";
$res_roomName .= grr_sql_query1($sql_roomName);

//Ici, $mail_object va recevoir la valeur de grr_mail_object de la table setting
$mail_object = Settings::get("grr_mail_object");

$mail_entete  = "MIME-Version: 1.0\r\n";
$mail_entete .= "From: {$_POST['nom']} "."<{$_POST['email']}>\r\n";
$mail_entete .= 'Reply-To: '.$_POST['email']."\r\n";
$mail_entete .= 'Content-Type: text/plain; charset="utf8"';
$mail_entete .= 'Content-Transfer-Encoding: 8bit';
$mail_entete .= "\r\nContent-Transfer-Encoding: 8bit\r\n";
$mail_entete .= 'X-Mailer:PHP/' . phpversion()."\r\n";

$mail_corps  = "Message de : " .$_POST['prenom']." " .$_POST['nom']." <br/>";
$mail_corps  .= "Email : ".$_POST['email']." <br/>";
$mail_corps  .= "Téléphone : ".$_POST['telephone']." <br/>";
$mail_corps  .= "Sujet de la réservation : ".$_POST['sujet']." <br/>"; 
$mail_corps  .= "Domaine : ".$res_areaName. " <br/>";
$mail_corps  .= "Ressource : ".$res_roomName. " <br/>";

if($_POST['end_day']){
	$mail_corps  .= "À réservé le ".$_POST['start_day']."/".$_POST['start_month']."/".$_POST['start_year']." à ".$_POST['start_']." <br/>";
	$mail_corps  .= "Jusqu'au ".$_POST['end_day']."/".$_POST['end_month']."/".$_POST['end_year']." à ".$_POST['end_']." <br/>";
}else{
	$mail_corps  .= "À réservé le ".$_POST['start_day']."/".$_POST['start_month']."/".$_POST['start_year']." à ".$_POST['start_']. " <br/>";
	$mail_corps  .= "Durée de la réservation : ".$_POST['end_']." min";
}

$mail_destinataire = Settings::get("mail_destinataire"). " \n";
$mail_method= Settings::get("grr_mail_method");

//Ici, on a fait une erreur volontaire pour passer au else pour tester le phpmailer
//if($mail_method == 'mail') {
if($mail_method =='mails'){
	//Pour l'objet de l'email qui est 'Demande de réservation', le caractère spécial 'é' ne passe pas
	if(mail($mail_destinataire, $mail_object, $mail_corps, $mail_entete)){
	header('Location: week_all.php');
	}else{
	echo "le message n'a pas été envoyé et donc mail n'est pas installé";
	}		

}else{
	require 'phpmailer/PHPMailerAutoload.php';
	define("GRR_FROM",Settings::get("grr_mail_from"));
	define("GRR_FROMNAME",Settings::get("grr_mail_fromname"));
	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
	$mail->Host = Settings::get("grr_mail_smtp");
	$mail->Port = 25;
	$mail->SMTPAuth = false;
	$mail->CharSet = 'UTF-8';
	$mail->setFrom(GRR_FROM, GRR_FROMNAME);
	$mail->SetLanguage("fr", "./phpmailer/language/");
	setlocale(LC_ALL, $locale);

	$mail->AddAddress($mail_destinataire);
	$mail->Subject = $mail_object;
	$mail->MsgHTML($mail_corps);
	$mail->AddReplyTo( $email_reponse );
	if (!$mail->Send())
		{
			$message_erreur .= $mail->ErrorInfo;
			echo $message_erreur;
		}
	else
	header('Location: week_all.php?area=1');
	}
?>
