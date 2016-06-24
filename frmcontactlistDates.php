<?php
include "include/connect.inc.php";
include "include/config.inc.php";
include "include/misc.inc.php";
include "include/functions.inc.php";
include "include/$dbsys.inc.php";
include "include/mincals.inc.php";
include "include/mrbs_sql.inc.php";
$grr_script_name = "contactFormulaire.php";
require_once("./include/settings.class.php");
if (!Settings::load())
	die("Erreur chargement settings");
require_once("./include/session.inc.php");
include "include/resume_session.php";
include "include/language.inc.php";

$id = $_GET['id'];

$res = grr_sql_query("SELECT type_affichage_reser FROM ".TABLE_PREFIX."_room WHERE id = '".$id."'");
$nbresult = mysqli_num_rows($res);
if ($nbresult != 0)
{
	for ($t = 0; ($row_roomName = grr_sql_row($res, $t)); $t++)
	{
		
		$tar =  $row_roomName[0];
	}
		
	
}
else{
	echo " <option value =\"1\">Aucune ressource liée à ce domaine</option>";
}	

			
if ($tar == "0") 
{
		
						
							
				?>
				
					<label>  Date : </label></br>
					
									
								<?php
								jQuery_DatePicker('start');
								?>
									</br>
								<label>  Durée : </label></br>
								
								
									<input class="form-control" type="text" id="duree" size="8" name="duree" placeholder="Durée en heure" required />
					
									<select id="dureemin" name="dureemin" class="form-control" >
									<option></option>
									<option> </option>
									</select>	
									<script>
										$("#dureemin").click(function(){
														 
    													remplirdureemin(resolution);

										});
									</script>	
								
	
	
						
	<?php
}
else
{
		
						
				
										
											jQuery_DatePicker('start');
										?>
									
								<label>  Heure début : </label>
								
								<?php
								
									echo " <select class =\"test\" name=\"heure\"> ";
										for ($h = 1 ; $h < 24 ; $h++)
											{
												echo "<option value =\"$h\"> ".sprintf("%02d",$h)."h </option>".PHP_EOL;
											}
									echo "</select>";
									echo " <select id = 'debdureemin' class =\"test\" name=\"minutes\"> </select>";
								
								?>
								</br>
								
										<?php
											jQuery_DatePicker('end');
										?>
									
								<label>  Heure de fin : </label>
								
								<?php
								
									echo " <select class =\"test\" name=\"heure\"> ";
										for ($h = 1 ; $h < 24 ; $h++)
											{
												echo "<option value =\"$h\"> ".sprintf("%02d",$h)."h </option>".PHP_EOL;
											}
									echo "</select>";
									echo " <select id = 'debdureemin' class =\"test\" name=\"minutes\"> </select>";
								
								
							
							}

?>
