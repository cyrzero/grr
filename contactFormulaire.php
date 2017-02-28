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
?>
<script type="text/javascript" src="js/functions.js"></script>
<?php
if ((Settings::get("authentification_obli") == 0) && (getUserName() == ''))
	$type_session = "no_session";
print_header("", "", "", $type="with_session");
bouton_retour_haut();
bouton_aller_bas();
?>
	<script>

		function remplirdureemin(res)
		  { 
		  
			frmContact.dureemin.options.length = 0;
			frmContact.debdureemin.options.length = 0;
			resmin = res/60;
			nbiteration = 60/resmin;
			
			var y= document.getElementById("debdureemin");
			var x = document.getElementById("dureemin");
			valeur = 0;

			for (i=0;i<nbiteration;i++){
				frmContact.dureemin.options[i] = document.createElement("option");
				frmContact.debdureemin.options[i] = document.createElement("option");
				
				if(i==0){
					valeur = 00;
				}else{
					valeur = valeur + resmin;
				}
					frmContact.dureemin.options[i].text = valeur +" min";
					frmContact.dureemin.options[i].value = valeur;
					x.add(frmContact.dureemin.options[i]);
				
					frmContact.debdureemin.options[i].text = valeur +" min";
					frmContact.debdureemin.options[i].value = valeur;
					y.add(frmContact.debdureemin.options[i]);
				}
			frmContact.dureemin.options.selectedIndex = 0;
		}
	</script>
	
	
	
	<form id="frmContact" method="post" action="traitementcontact.php">
	<div id="formContact">
		<div class="row">
			
			<fieldset>
				
				<legend><b>Vos coordonnées</b></legend>
					
				<div class="col-lg-6 col-md-6 col-xs-6">
					
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
							<input class="form-control" type="text" id="nom"  size="8" name="nom" placeholder="Votre nom" required/>
						</div>
					</div>
						
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
							<input class="form-control" type="text" size="8" id="prenom"  name="prenom" placeholder="Votre prénom" />
						</div>
					</div>
					
				</div>

				<div class="col-lg-6 col-md-6 col-xs-6">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></div>
							<input class="form-control" type="email" id="email" size="8" name="email" placeholder="Votre adresse de courriel" required />
						</div>
					</div>
					
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><span class="glyphicon glyphicon-earphone"></span></div>
							<input class="form-control" type="tel" size="8" maxlength="14" id="telephone" name="telephone" placeholder="Votre numéro de téléphone" required />
						</div>
					</div>
				</div>
				
		</fieldset>
	</div>
				
	<div class="row">

				<fieldset>
					<legend><b>Réservation</b></legend>
					<label for="subject">Sujet :</label>
					<textarea class="form-control" id="subject" name="sujet" cols="30" rows="4" required></textarea><br/>
					
					
					
						<label>Domaines : </label>

						<script>
						function getSelectValueArea(){
							var selectElmt = document.getElementById("area");
							return selectElmt.options[selectElmt.selectedIndex].value;
						}
						function getSelectValueRoom(){
							var selectElmt = document.getElementById("room");
							return selectElmt.options[selectElmt.selectedIndex].value;
						}
						
						
						</script>

						<select id="area" name="area" class="form-control" required>
							<option value="">SELECTIONNEZ UN DOMAINE </option>
							<?php
							$sql_areaName = "SELECT id, area_name,resolution_area FROM ".TABLE_PREFIX."_area ORDER BY area_name";
							$res_areaName = grr_sql_query($sql_areaName);
							for ($i = 0; ($row_areaName = grr_sql_row($res_areaName, $i)); $i++)
							{
								if (authUserAccesArea(getUserName(),$row_areaName[0]) == 1)
								{
									$id = $row_areaName[0];
									$area_name = $row_areaName[1];
									$resolution_area = $row_areaName[2];
									echo '<option onclick="" value="'.$id."/".$resolution_area.'"> '.$area_name.'</option>'.PHP_EOL;
								}
							}
							
							?>
						</select>
						<!-- modif Clement Talledec 
						Selection des données situé dans le fichier frmcontactlist.php qui renvoit en fonction de l'id la valeur du room area -->

						<script>
							$(document).ready(function()
							{
								var $domaine = $('#area');
								var $salle = $('#room');
								$domaine.on('change', function()
								{	
									var select = $(this);
									var values = select.find(":selected").attr("value");
									var value = values.split('/');
									var id = value[0] ;
									var resolution = value[1] ;
									
									remplirdureemin(resolution);
									//~ remplirdebdureemin(resolution);
									if (id != '')
									{
										$salle.empty();
										jQuery.ajax({
											type: 'GET',
											url: 'frmcontactlist.php',
											data: {
												id: id
											},
											success: function(returnData)
											{
												$('#room').html(returnData);
												return(resolution);
											},
											error: function(returnData)
											{
												alert('Erreur lors de l execution de la commande AJAX  ');
											}
										});
									}
									else{
										clearOptions('room');
									}
								});
							});
						</script>

						<label>Ressources : </label>

						<select id="room" name="room" class="form-control" required>
							<optgroup label="Salles">
								<option>SELECTIONNEZ UNE RESSOURCE </option>
							</select>
							<!-- modif  Chauvin Jérémy-->
						<script>
							//recupére id de l'area(domaine) selectionné ainsi que la room(ressource)
							getSelectValueArea();
							getSelectValueRoom();
						function fct() {
							var idarea = getSelectValueArea();
							var idRoom = getSelectValueRoom();
							//alert(area); // afficher ID
							document.location.href="contactFormulaire.php?area="+idarea+"&nameRoom="+idRoom;
							
						}
						</script>
						<?php
						//modif  Chauvin Jérémy
						
						/*if (isset($_GET['valider'])) { 
							// Récupération de la variable 
							$nb = $_GET['area']; // La variable est récupéré à partir de l'url
						}*/
						
						//modif_Elodie
						//$idArea = isset($_GET["area"]) ? $_GET["area"] : NULL;
						//$id_room = isset($_GET["nameRoom"]) ? $_GET["nameRoom"] : NULL;
						
						$idArea = $_GET['area'];
						//addslashes sur le nameRoom car contient apostrophe
						$id_room = addslashes($_GET['nameRoom']);
						if($idArea != ""){
								$overload_fields = overloadWithRoom($idArea,$id_room);
							foreach ($overload_fields as $fieldname=>$fieldtype)
							{
								if ($overload_fields[$fieldname]["obligatoire"] == "y")
									$flag_obli = " *" ;
								else
									$flag_obli = "";
								echo "<table width=\"100%\" id=\"id_".$areas."_".$overload_fields[$fieldname]["id"]."\">";
								echo "<tr><td class=E><b>".removeMailUnicode($fieldname).$flag_obli."</b></td></tr>\n";
								if (isset($overload_data[$fieldname]["valeur"]))
									$data = $overload_data[$fieldname]["valeur"];
								else
									$data = "";
								if ($overload_fields[$fieldname]["type"] == "textarea" )
									echo "<tr><td><div class=\"col-xs-12\"><textarea class=\"form-control\" cols=\"80\" rows=\"2\" name=\"addon_".$overload_fields[$fieldname]["id"]."\">".htmlspecialchars($data)."</textarea></div></td></tr>\n";
								else if ($overload_fields[$fieldname]["type"] == "text" )
									echo "<tr><td><div class=\"col-xs-12\"><input class=\"form-control\" size=\"80\" type=\"text\" name=\"addon_".$overload_fields[$fieldname]["id"]."\" value=\"".htmlspecialchars($data)."\" /></div></td></tr>\n";
								else if ($overload_fields[$fieldname]["type"] == "numeric" )
									echo "<tr><td><div class=\"col-xs-12\"><input class=\"form-control\" size=\"20\" type=\"text\" name=\"addon_".$overload_fields[$fieldname]["id"]."\" value=\"".htmlspecialchars($data)."\" /></div></td></tr>\n";
								else
								{
									echo "<tr><td><div class=\"col-xs-12\"><select class=\"form-control\" name=\"addon_".$overload_fields[$fieldname]["id"]."\" size=\"1\">\n";
									if ($overload_fields[$fieldname]["obligatoire"] == 'y')
										echo '<option value="">'.get_vocab('choose').'</option>';
									foreach ($overload_fields[$fieldname]["list"] as $value)
									{
										echo "<option ";
										if (htmlspecialchars($data) == trim($value,"&") || ($data == "" && $value[0]=="&"))
											echo " selected=\"selected\"";
										echo ">".trim($value,"&")."</option>\n";
									}
									echo "</select></div>\n</td></tr>\n";
								}
								echo "</table>\n";
							}
						}
						//fin ici
						?>
				</fieldset>
				
		</div>
				
				
		<div class="row">	

							<select id = "debdureemin" style="display:none" class ="test" name="minutes"> </select>

							<p style="display:none">	
							<input class="form-control" type="text" id="duree" size="8" name="duree" placeholder="Durée en heure" />
								
							<select id="dureemin" style="display:none" name="dureemin" class="form-control">
								<option> </option>
								<option> </option>
								
							</select>
							</p>

<!-- modif Clement Talledec 
	Selection des données situé dans le fichier frmcontactlistDates.php qui renvoit en fonction de le premier formulaire dates ou le second formulaire date pour le nombre de jour -->

					<script>
						$(document).ready(function() {					
								var $salle = $('#room');
								var $Dates = $('#Dates');
								var $domaine = $('#area');
								
								$salle.on('change', function() {	
									var select = $(this);
									var values = select.find(":selected").attr("value");
									var value = values.split('/');
									var id = value[0] ;
																		
									if (id != '') {
										$Dates.empty();
										jQuery.ajax({
											type: 'GET',
											url: 'frmcontactlistDates.php',
											data: {
												id: id
											},
											dataType : 'html',
											success: function(returnData) {
												$('#Dates').html(returnData);			
											},
											error: function(returnData) {
												alert('Erreur lors de l execution de la commande AJAX  ');
											}
										});
									}
									else {
										clear('Dates');
									}
								});

							});
					</script>

				<div id = "Dates" ></div>
<!--
			</fieldset>
-->
		<br/>
		<br/>

			<div id="buttonsReservation" style="">
				<input class="btn btn-primary" type="submit" name="submit" value="Envoyer la demande de réservation">

				<input class="btn btn-primary" type="button" name="retouraccueil" value="Retour à l'accueil" onClick="javascript:location.href='javascript:history.go(-1)'">
			</div>
	</div>

	<div id="toTop">
	<?php echo get_vocab('top_of_page'); ?>
	</div>

	
	<div id="toBot">
	<?php echo get_vocab('bot_of_page'); ?>
	</div>
	
	<script>
		jQuery(document).ready(function() {
			jQuery("#frmContact").validate({
			  rules: {
					"email": {
					"email": true,
					"maxlength": 255
					},
					"telephone": {
					"required": true,
					"digits" : true,
					"maxlength": 10
					},
					"duree": {
					"required": true,
					"digits" : true
					},
					"area": {
					"required": true,
					"value" 
					},
					"room": {
					"required": true
					},
					"subject": {
					"required": true
					},
				 }
			})
		});
		jQuery.extend(jQuery.validator.messages, {
			required: "Ce champs est obligatoire !",
			remote: "votre message",
			email: "Format d'Email invalide !",
			digits:"Ce champs n'accepte que des chiffres."
		});
	</script>
	
</div>
</form>

</body>
</html>
