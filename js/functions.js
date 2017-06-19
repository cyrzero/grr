// Permet de faire une validation afin que l'usager ne puisse pas s�lectionner un jour invalide pour le d�but du premier Jours/Cycle
function verifierJoursCycles()
{
	valeurA = document.getElementById('jourDebut').value;
	valeurB = document.getElementById('nombreJours').value;
	if (parseInt(valeurA) > parseInt(valeurB))
	{
		alert('La date du premier jour est invalide');
		return false;
	}
	else
	{
		return true;
	}
}
function clicMenu(num)
{
	var fermer;var ouvrir;var menu;
	menu = document.getElementById('menu' + num);
	if (document.getElementById('fermer'))
		fermer = document.getElementById('fermer');
	if (document.getElementById('ouvrir'))
		ouvrir = document.getElementById('ouvrir');
	// On ouvre ou ferme
	if (menu)
	{
		if (menu.style.display == "none")
		{
			if (fermer)
				fermer.style.display = "";
			if (ouvrir)
				ouvrir.style.display = "none";
			menu.style.display = "";
		}
		else
		{
			menu.style.display = "none";
			if (fermer)
				fermer.style.display = "none";
			if (ouvrir)
				ouvrir.style.display = "";
		}
	}
}
function centrerpopup(page,largeur,hauteur,options)
{
// les options :
//    * left=100 : Position de la fen�tre par rapport au bord gauche de l'�cran.
//    * top=50 : Position de la fen�tre par rapport au haut de l'�cran.
//    * resizable=x : Indique si la fen�tre est redimensionnable.
//    * scrollbars=x : Indique si les barres de navigations sont visibles.
//    * menubar=x : Indique si la barre des menus est visible.
//    * toolbar=x : Indique si la barre d'outils est visible.
//    * directories=x : Indique si la barre d'outils personnelle est visible.
//    * location=x : Indique si la barre d'adresse est visible.
//    * status=x : Indique si la barre des status est visible.
//
// x = yes ou 1 si l'affirmation est vrai ; no ou 0 si elle est fausse.
var top = (screen.height - hauteur) / 2;
var left = (screen.width - largeur) / 2;
window.open(page,"","top="+top+",left="+left+",width="+largeur+",height="+hauteur+",directories=no,toolbar=no,menubar=no,location=no,"+options);
}
/**
 * Displays an confirmation box beforme to submit a query
 * This function is called while clicking links
 *
 * @param   object   the link
 * @param   object   the sql query to submit
 * @param   object   the message to display
 *
 * @return  boolean  whether to run the query or not
 */
 function confirmlink(theLink, theSqlQuery, themessage)
 {
 	var is_confirmed = confirm(themessage + ' :\n' + theSqlQuery);
 	if (is_confirmed) {
 		theLink.href += '&js_confirmed=1';
 	}
 	return is_confirmed;
 }
// end of the 'confirmLink()' function
function confirmButton(theform,themessage)
{
	var is_confirmed = window.confirm(themessage);
	if (is_confirmed)
	{
		document.forms[theform].submit();
	}
	return is_confirmed;
}
// end of the 'confirmButton()' function
/**
 * Checks/unchecks les boites � cocher
 *
 * the_form   string   the form name
 * do_check   boolean  whether to check or to uncheck the element
 * day la valaur de de la boite � cocher ou � d�cocher
 * return  boolean  always true
 */
 function setCheckboxesGrr(elts, do_check, day)
 {
 	for (i = 0; i < elts.length; i++)
 	{
 		type = elts.type;
 		if (type="checkbox")
 		{
 			if ((elts[i].value== day) || (day=='all'))
 			{
 				elts[i].checked = do_check;
 			}
 		}
 	}
 	return true;
} // end of the 'setCheckboxes()' function
function _setCheckboxesGrr(the_form, do_check, day)
{
	var elts = document.forms[the_form];
	for (i = 0; i < elts.elements.length; i++)
	{
		type = elts.elements[i].type;
		if (type="checkbox")
		{
			if ((elts.elements[i].value== day) || (day=='all'))
			{
				elts.elements[i].checked = do_check;
			}
		}
	}
	return true;
}
// end of the 'setCheckboxes()' function
// Les quatre fonctions qui suivent servent � enregistrer un cookie
// Elles sont utilis� par edit_enty.php pour conserver les informations de la saisie pour
// pouvoir les r�cup�rer lors d'une erreur.
//Hugo
// Voir http://www.howtocreate.co.uk/jslibs/script-saveformvalues
var FS_INCLUDE_NAMES = 0, FS_EXCLUDE_NAMES = 1, FS_INCLUDE_IDS = 2, FS_EXCLUDE_IDS = 3, FS_INCLUDE_CLASSES = 4, FS_EXCLUDE_CLASSES = 5;
//Hugo - fonction qui r�cup�re les informations des champs input pour les stocker dans un cookie (Voir http://www.howtocreate.co.uk/jslibs/script-saveformvalues)
function getFormString( formRef, oAndPass, oTypes, oNames )
{
	if (oNames)
	{
		oNames = new RegExp((( oTypes > 3 )?'\\b(':'^(')+oNames.replace(/([\\\/\[\]\(\)\.\+\*\{\}\?\^\$\|])/g,'\\$1').replace(/,/g,'|')+(( oTypes > 3 )?')\\b':')$'),'');
		var oExclude = oTypes % 2;
	}
	for (var x = 0, oStr = '', y = false; formRef.elements[x]; x++)
	{
		if (formRef.elements[x].type)
		{
			if (oNames)
			{
				var theAttr = (oTypes > 3) ? formRef.elements[x].className : ((oTypes > 1) ? formRef.elements[x].id : formRef.elements[x].name);
				if ((oExclude && theAttr && theAttr.match(oNames)) || (!oExclude && !( theAttr && theAttr.match(oNames))))
				{
					continue;
				}
			}
			var oE = formRef.elements[x];var oT = oE.type.toLowerCase();
			if (oT == 'text' || oT == 'textarea' || ( oT == 'password' && oAndPass ) || oT == 'datetime' || oT == 'datetime-local' || oT == 'date' || oT == 'month' || oT == 'week' || oT == 'time' || oT == 'number' || oT == 'range' || oT == 'email' || oT == 'url')
			{
				oStr += ( y ? ',' : '' ) + oE.value.replace(/%/g,'%p').replace(/,/g,'%c');
				y = true;
			}
			else if (oT == 'radio' || oT == 'checkbox')
			{
				oStr += (y ? ',' : '') + (oE.checked ? '1' : '');
				y = true;
			}
			else if (oT == 'select-one')
			{
				oStr += (y ? ',' : '') + oE.selectedIndex;
				y = true;
			}
			else if (oT == 'select-multiple')
			{
				for (var oO = oE.options, i = 0; oO[i]; i++ )
				{
					oStr += (y ? ',' : '') + (oO[i].selected ? '1' : '');
					y = true;
				}
			}
		}
	}
	return oStr;
}
//Hugo - Fonction qui r�cup�re les informations stock�es de le cookie pour les remttres dans les inputs (Voir http://www.howtocreate.co.uk/jslibs/script-saveformvalues)
function recoverInputs( formRef, oStr, oAndPass, oTypes, oNames )
{
	if (oStr)
	{
		oStr = oStr.split( ',' );
		if (oNames)
		{
			oNames = new RegExp((( oTypes > 3 )?'\\b(':'^(')+oNames.replace(/([\\\/\[\]\(\)\.\+\*\{\}\?\^\$\|])/g,'\\$1').replace(/,/g,'|')+(( oTypes > 3 )?')\\b':')$'),'');
			var oExclude = oTypes % 2;
		}
		for (var x = 0, y = 0; formRef.elements[x]; x++ )
		{
			if (formRef.elements[x].type)
			{
				if (oNames)
				{
					var theAttr = ( oTypes > 3 ) ? formRef.elements[x].className : ( ( oTypes > 1 ) ? formRef.elements[x].id : formRef.elements[x].name );
					if ((oExclude && theAttr && theAttr.match(oNames)) || (!oExclude && (!theAttr || !theAttr.match(oNames))))
					{
						continue;
					}
				}
				var oE = formRef.elements[x];var oT = oE.type.toLowerCase();
				if (oT == 'text' || oT == 'textarea' || (oT == 'password' && oAndPass) || oT == 'datetime' || oT == 'datetime-local' || oT == 'date' || oT == 'month' || oT == 'week' || oT == 'time' || oT == 'number' || oT == 'range' || oT == 'email' || oT == 'url' )
				{
					oE.value = oStr[y].replace(/%c/g,',').replace(/%p/g,'%');
					y++;
				}
				else if (oT == 'radio' || oT == 'checkbox')
				{
					oE.checked = oStr[y] ? true : false;
					y++;
				}
				else if ( oT == 'select-one')
				{
					oE.selectedIndex = parseInt( oStr[y]);
					y++;
				}
				else if ( oT == 'select-multiple')
				{
					for (var oO = oE.options, i = 0; oO[i]; i++ )
					{
						oO[i].selected = oStr[y] ? true : false;
						y++;
					}
				}
			}
		}
	}
}
function retrieveCookie(cookieName)
{
	/* retrieved in the format
	cookieName4=value; cookieName3=value; cookieName2=value; cookieName1=value
	only cookies for this domain and path will be retrieved */
	var cookieJar = document.cookie.split( "; " );
	for( var x = 0; x < cookieJar.length; x++ )
	{
		var oneCookie = cookieJar[x].split( "=" );
		if (oneCookie[0] == escape(cookieName))
		{
			return oneCookie[1] ? unescape(oneCookie[1]) : '';
		}
	}
	return null;
}
function setCookie(cookieName, cookieValue, lifeTime, path, domain, isSecure)
{
	if (!cookieName)
	{
		return false;
	}
	if (lifeTime == "delete" )
	{
		lifeTime = -10;
	}
	//this is in the past. Expires immediately.
	/* This next line sets the cookie but does not overwrite other cookies.
	syntax: cookieName=cookieValue[;expires=dataAsString[;path=pathAsString[;domain=domainAsString[;secure]]]]
	Because of the way that document.cookie behaves, writing this here is equivalent to writing
	document.cookie = whatIAmWritingNow + "; " + document.cookie; */
	document.cookie = escape(cookieName) + "=" + escape(cookieValue) + (lifeTime ? ";expires=" + (new Date((new Date()).getTime() + (1000 * lifeTime))).toGMTString() : "") + (path ? ";path=" + path : "") + (domain ? ";domain=" + domain : "") + (isSecure ? ";secure" : "");
	//check if the cookie has been set/deleted as required
	if ( lifeTime < 0 )
	{
		if (typeof(retrieveCookie(cookieName)) == "string")
		{
			return false;
		}
		return true;
	}
	if (typeof(retrieveCookie(cookieName)) == "string")
	{
		return true;
	}
	return false;
}
/* fonction qui est utilis�e pour basculer un �l�ment d'une liste1 vers une liste2 et inversement (utilis� lors de la cr�ation d'une demande) */
function Deplacer(liste1, liste2)
{
	while (liste1.options.selectedIndex >= 0)
	{
		opt = new Option(liste1.options[liste1.options.selectedIndex].text,liste1.options[liste1.options.selectedIndex].value);
		liste2.options[liste2.options.length] = opt;
		liste1.options[liste1.options.selectedIndex] = null;
	}
}
function vider_liste(IdListe)
{
	var l = IdListe.options.length;
	for (var i = 0; i < l; i++)
	{
		IdListe.options[i] = null;
	}
}
function selectionner_liste(IdListe)
{
	var l = IdListe.options.length;
	for(var i = 0; i < l; i++)
	{
		IdListe.options[i].selected = true;
	}
}
/*-----MAJ Lo�s THOMAS  --> Fonctions qui permettent de cacher et afficher le menu � gauche -----*/
function divaffiche(month_all2)
{
	var Nbr = month_all2;
	if ( Nbr == 1)
	{
		document.getElementById("menuGaucheMonthAll2").style.display = "block";
		document.getElementById("planningMonthAll2").style.marginLeft = "300px";
		document.getElementById("planningMonthAll2").style.width = "auto";
	}
	else
	{
		document.getElementById("menuGauche").style.display = "block";
		document.getElementById("planning").style.marginLeft = "300px";
		document.getElementById("planning").style.width = "auto";
	}
	document.getElementById("cacher").style.display = "inline";
	document.getElementById("voir").style.display = "none";
}
function divcache(month_all2)
{
	var Nbr = month_all2;
	if (Nbr == 1)
	{
		document.getElementById("menuGaucheMonthAll2").style.display = "none";
		document.getElementById("planningMonthAll2").style.marginLeft = "0px";
		document.getElementById("planningMonthAll2").style.width = "auto";
	}
	else
	{
		document.getElementById("menuGauche").style.display = "none";
		document.getElementById("planning").style.marginLeft = "0px";
		document.getElementById("planning").style.width = "auto";
	}
	document.getElementById("cacher").style.display = "none";
	document.getElementById("voir").style.display = "inline";
}
function afficherMoisSemaine(a)
{
	var Nb = a;
	document.getElementById('afficherBoutonSelection'+Nb).style.display = "none";
	document.getElementById('cacherBoutonSelection'+Nb).style.display = "inline";
	document.getElementById('boutonSelection'+Nb).style.display = "inline";
}
function cacherMoisSemaine(a)
{
	var Nb = a;
	document.getElementById('cacherBoutonSelection'+Nb).style.display = "none";
	document.getElementById('afficherBoutonSelection'+Nb).style.display = "inline";
	document.getElementById('boutonSelection'+Nb).style.display = "none";
}

function charger(){
	var test = document.getElementById("chargement");
	test.style.display = 'Block';
}

function clearOptions(id){
	document.getElementById(id).options.length = 0;
	document.getElementById(id).innerHTML= '<optgroup label="Salles"><option value="">Selectionnez une ressource </option>'
}

//lance l'impression apr�s clic sur bouton et si configur� pour
function printPage(optionPrint){
	console.log("test print");
	if((window.location.href.indexOf("&pview=1&precedent=1") > -1) & (optionPrint == 1)){
       window.print();
       console.log("impression");
    }
    else{
		console.log("pas d'impression");
	}
}
//==Gestion des fichiers joints==
//upload des fichiers.
function uploadFiles(){

	var myForm = document.getElementById("uploadForm");
	var progress = document.getElementById("avancement");
	var infos = document.getElementById("infos");

	myForm.onsubmit = function(event){

		//d�sactive le traitement normal du formulaire
		event.preventDefault();

		//dispo de formData
		if(window.FormData){
			var fd = new FormData();
		}
		else{
			alert("FormData non support� par votre navigateur");
			return;
		}

		//init ajax
		var xhr = new XMLHttpRequest();

		//parametre de la requ�te
		//myForm.getAttribute("action") = "upload.php";
		xhr.open("post", myForm.getAttribute("action"), true);

		xhr.onreadystatechange = function(event){
			if(this.readyState == 4){
				//affichage du retour texte de la requ�te
				infos.innerHTML += event.target.responseText;
			}
		};
		//sur la progression
		xhr.onprogress = function(event){
			if(event.lengthComputable){
				var pourcentage = Math.round(event.loaded*100/event.total);
				progress.setAttribute("aria-valuenow", pourcentage);
				progress.value = pourcentage;
			}
		};

		//liste des fichiers et id � envoyer
		var inputFiles = document.getElementById('myFiles');
		var fichiers = inputFiles.files;

		for(i=0; i<fichiers.length; i++){
			infos.innerHTML += "Envoi de "+fichiers[i].name+"...<br>";
			fd.append(inputFiles.name, fichiers[i]);
		}
		var id_index = document.getElementById('id_entry');
		fd.append("id_entry", id_index.value);
		//envoi des donn�es
		xhr.send(fd);
	};
}

function loadFile(){
	// v�rifie si une ligne est s�lectionn�e
	var target = document.getElementById("SelectFile");
	if(target.selectedIndex == -1){
		alert("Veuillez selectionner un fichier")
	}
	else{
		//r�cup�re le nom r�el du fichier et transmet la demande de r�cup�ration.
		var fileSelected = target.options[target.selectedIndex].value;
		console.log(fileSelected);
		window.location.href = 'download_files.php?name='+fileSelected;
	}
}


function deleteFile(id,rowIndex,idFile){
	var dtarget = document.getElementById("table_supprimer");
		if (confirm("Voulez-vous vraiment supprimer ce fichier ?")){
			//pr�paration de l'ajax pour la suppression
			var retourInfos = document.getElementById("retourInfos");
			if (window.FormData){
				var fData = new FormData();
			}
			else{
				alert ("FormData non support�");
				return;
			}
			var varAjax = new XMLHttpRequest();
			varAjax.open("post", "deleteFile.php", true);
			varAjax.onreadystatechange = function(event){
				if(this.readyState == 4){
					retourInfos.innerHTML += event.target.responseText;
					dtarget.deleteRow(rowIndex);
				}
			};


			//pr�paration des infos de suppression � transmettre.
			fData.append("idFile", idFile);
			fData.append("id", id);

			varAjax.send(fData);
		}
		else{
			return;
		}

}
