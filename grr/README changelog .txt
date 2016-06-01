Chauvin Jérémy
v2.5.1
12/02/2016

--Gestion des Overloads
* Ajout de fonctionnalité permettant de placer un overload sur une ressource (room) et non plus seulement sur un domaine(area)
* Modification de la base de données avec ajout d'un champs "id_room" dans la table 'grr_overlad'
* Possibilité de créer un overload sur une ressource (Dans l'interface d'administration)
* Affichage de l'overload sur la ressource dans "edit_entry" et "contactFormulaire"

--Mise en place d'un dossier "Admin" contenant tous les fichiers administrateurs
* Changement de tous les chemins contenues dans les fichiers Admin pour qu'il soit à nouveau correct

--Sécurité
* Correction de failles XSS

Anthony Archambeau

V2.3 Changelog 
09/02/2015

Cette version s'appuie sur les contributions d'Étienne Lagacé et Florian OTHON aportée dans 
la version disponible sur cette adresse https://github.com/Sirlefou1/GRR2. 

-- Régréssions liées aux besoins de la mairie de Talmont-Saint-Hilaire: 
 
* Désactivation de la gestion des clefs et des courriers 
* Consultation libre ( sans connexion ) aux domaines non restraints 
* Désactivation du bouton " menu/\" 
* Suppression du fond noir lors de l'ouverture d'un fenêtre de détails d'une réservation.

-- MAJ fonctionalitées 

*Mise au point de la génération de PDFs, en prenant en compte si la réservation est périodique ou non.
*Prise en compte effective du theme séléctionné dans l'interface d'administration.
*Ajout de plusieurs thèmes.
*Mise au point du formulaire de contact :
	- Affichage de la ressource correspondant au domaine séléctionné.
	- Prise en compte de la résolution du domaine (15-30 ou 60min). 
	- Mise en forme.
	- Prise en compte de la méthode d'envoie des mails séléctionnée dans l'administration ( Mail ou STMP).   
	- Ajout de boutons pour se déplacer rapidement en haut et en bas du formulaire.
	- Contrôle de saisies (Vérifications du format des champs e-mail,téléphone, durée).
	
* Mise en place du responsive design : le site s'adapte désormais correctement selon la résolution (Téléphone,tablette,petit écran,large..).
* Ajout de traductions.
* Optimisation de la fenêtre modale comportant les détails des réservations :
	- Possibilité de la déplacer dans les limites de l'écran.
	- Centrage automatique selon le type d'affichage ( Téléphone ,tablette, écran ).
* Réglages pour une meilleure mise à jour depuis les anciennes versions de GRR. 	

-- Débugage

* Les heures de début et de fin sont correctement prises en compte lors de l'édition des réservation.
* L'affichage des résevations s'étalant sur plus de deux jours s'éffectue désormais correctement.
* Reglage d'un bug d'affichage dans day.php.( Lors d'un clique sur le nom d'une ressource celui ci se dédoublait) 
* Saut de ligne dans les boutons du menu ressource lorsque le nom d'une ressource est trop long et dépasse. 
* Correction de la couleur du texte des réservations dans month.php.
* Correction des tableaux pour permettre une meilleur impression (économie de lignes et donc de pages).
* Prise en compte de l'UTF8 dans la génération des PDFs.


Étienne Lagacé/Florian OTHON
V2.2.2/3 Changelog 

[Correction] mktine() vers time()
[Correction] Fonction MySQL_ vers mysqli
[Correction] Remise en place des jours et heures des réservations lors de l’édition
[Correction] Correction de minical sauvegarde du choix de la salle et du type d'affichage
[Correction] Balise PHP courte dans view_entry.php
[Correction] undefined index dans mincals.php
[Correction] undefined index dans edit_entry.php
[Correction] undefined index dans view_entry.php
[Correction] undefined index dans menu_gauche.php
[Correction] undefined index dans day.php
[Correction] undefined index dans week.php
[Correction] undefined index dans week_all.php
[Correction] undefined index dans month.php
[Correction] undefined index dans month_all.php
[Correction] undefined index dans month_all2.php
[Correction] undefined variable dans functions.inc.php
[Correction] Encodage des caractères UTF-8 des pages
[Correction] Encodage des caractères UTF-8 des pages en pop-up
[Correction] Encodage des caractères UTF-8 des fichiers générés
[Correction] affichage des pages de modération avec l'option pop-up
[Correction] CSS print
[Correction] Traductions
[Correction] session/traduction sur certaines pages
[Correction] affichage du menu sur certaines pages de l'admin
[Correction] Balise PHP courte dans contactFormulaire.php
[Correction] Affichage réservation sur 2 jours
[Correction] bouton aujourd'hui de mincals
[Ajout] gestion des droits dans contactFormulaire.php
[Ajout] bootstrap 3.3.1
[Ajout] fonction clé donnée/rendu
[Ajout] fonction courrier reçu
[Ajout] choix de réservation périodique chaque X ème Y jour du mois
[Ajout] Jours fériés
[Ajout] Vacances scolaire française
[Ajout] paramètres pour les jours fériés et vacances
[Ajout] Champs de recherche dans le select des bénéficiaires de edit_entry.php
[Màj] lib JQuery 2.1.1
[Màj] script popup.js compatibilité jQuery
[Màj] fonction bouton_retour_haut() compatibilité jQuery
[Màj] lib jquery.validate.js
[Màj] lib Ckeditor 4.4.5
[Màj] lib JQuery-UI 1.11.1
[Màj] lib DatePicker
[Màj] lib TimePicker 1.5.0
[Màj] lib PHP mailer 5.2.9+
[Màj] lib JsPDF master-43eb081
[Màj] Traductions
[Changement] Correction diverse du code HTML générer W3c validation
[Changement] Ordre de chargement des fichiers css et js
[Changement] Création et déplacement des fichiers js dans le dossier js
[Changement] Nettoyage du code / Mise en forme
[Changement] Apparence utilisation de bootstrap et de glyphicon
[Changement] Simplification de la fonction grr_sql_version par l'utilisation de mysqli_get_server_info
[Changement] Horloge mise à jour pour fonctionner sur chrome
[Changement] Affichage du menu gauche dans toutes les pages de l'administration
[Changement] Passage de la vérification des droits via une fonction
[Changement] timepicker to clockpicker
[Changement] Génération de PDF avancé
[Changement] optimisation du mincals
[Changement] suppression des variables inutile dans showaccessdenied
[Changement] Gestion des paramètres via une class Settings
[Suppression] Fichiers inutile
[Suppression] fonction html_entity_decode_all_version
[Suppression] fragments de code pour l'upload des fichiers


V2.2.1 Changelog
04/06/2014

- Correction de l'installateur et nettoyage du code.


Hugo Forestier
11/06/2013

- Mise a jour mineure : résolution du bug du TimePicker empechant de creer une réservation lorsqu'il fallait renseigner la date de debut et la date de fin de la réservation



05/06/2013
V2.2 Changelog 

- Mise en place d'un calendrier (de type DatePicker) utilisant jQuery dans la page edit_entry.php
- Mise à jour du datePicker pour afficher les dates au format français (jj/mm/aaaa) en commançant la semaine le lundi (et non le dimanche)
- Mise en place d'un TimePicker utilisant jQuery dans la page edit_entry.php
- Ajout d'une grille et d'un pas de 15 minutes au TimePicker
- Résolution du bug de la fonction TimePicker lors de la modification d'une réservation
- Traduction en français du datePickeret du TimePicker
- Mise à jour du logo
- Optimisation de la fonction genDateSelectorForm, les numéros générés ne seront plus de la forme 1,2,3.. mais de la forme 01,02,03.. pour éviter les bugs avec le datePicker
- Correction des fautes d'orthographe
- Ajouts de commentaires pour une meilleure compréhension de la fonction validate_and_submit de edit_entry.php
- Traduction dans toutes les langues de la page d'administration pour l'option "format de popup" et "affichage ou non du menu de gauche"
- Remplacement de la bibliothèque prototype par les bibliothèques jQuery, datePicker et TimePicker
- Optimisation des fonctions DatePicker et TimePicker pour éviter les bugs en cas d'informations manquantes
- Optimisation de la vitesse de chargement des pages grâce au stockage en local des bibliothèques minimales jQuery 



23/05/2013
V2.1 Changelog 

- Modification de l'interface : Suppression du bouton "haut de page" non fonctionnel
- Modification de l'interface : Création d'un nouveau bouton "haut de page" fonctionnel en jQuery
- Modification de l'interface : Ajout d'une option dans la page d'administration pour l'affichage ou non du bouton "cacher le menu de gauche" 
- Réparation du bouton "Jour précédent" et "Jour suivant" ne fonctionnant pas dans la page day.php


Loïs Thomas
v2.0 Changelog

Mise a jour faite dans le but d'une installation tactile et d'une amélioration de l'ergonomie.
- Modifications css,
- Liens sous forme d'item afin de faciliter l'utilisation du tactile,
- Ajouts des heures des RDV sur les plannings,
- Ajout du genre sur les plannings,
- Adaptation du calendrier pour facilitier l'utilisation du tactile,
- Possibilité de choisir la langues xpour tout visiteurs,
- Création de l'horloge pour toutes les langues,
- Possibilité de supprimer la légendes dans la configuration
- Désactivé la selection sur les différents pages pour les utilisateurs non connecté. 
- Ajout d'une barre de chargement 
- Ajout du genre des réservations pour les pages : (day.php, month.php, month_all.php, week_all.php, week.php)
- Création d'un lien précédent sur la page login.php 
- Création de lien précédent au format imprimable pour les pages : (day.php, month.php, month_all.php, week_all.php, week.php)
- Création des fonctions : make_site_item_html(...) sur le modéle de make_site_select_html, make_area_item_html, make_room_item_html sur le modéle de make_room_select_html()
- Choix de l'affichage des réservations.Soit dans une page(view_entry.php) ou sous forme d'un popup
- Choix de l'affichage de la légende ou non.






