<?php

$messages = array(
	
	// Erreurs 'fatales'
	'error' => 'FEHLER :',
	'nodbuse' => 'RegexMania n\'est pas configuré pour enregistrer les expressions régulières.',
	'entername' => 'Veuillez donner un nom à la regex.',
	'enterpass' => 'Une regex avec ce nom existe déjà. Veuillez enter un autre nom ou entrer le mot de passe utilisé lors de l\'enregistrement.',
	'dbalreadyok' => 'La base de données est déjà prête à être utilisée',
	'nodbserverconnexion' => 'Impossible de se connecter au serveur MySQL. Veuillez vérifier les paramètres de la base de données dans le script (nom d\'hôte, nom d\'utilisateur et mot de passe associé).',
	'nodbconnexion' => 'Impossible de se connecter à la base de données.',
	
	// Écran principal
	'cribsheet' => 'Kurzer Abriss',
	'listregexes' => 'Liste der Augzeichnungen',
	'compute' => 'Berechnen',
	'save' => 'Aufzeichnen',
	'name' => 'Name :',
	'comment' => 'Kommantare:',
	'password' => 'Passwort:',
	'protectregex' => 'schützen regex',
	'open' => 'Öffnen',
	'total' => 'Total:',
	'erase' => 'Löschen:',
	'eraseallregexes' => 'alle regex',
	'erasetext' => 'der Text',
	'result' => 'Ergebnis:',
	'textoforigin' => 'Quelletext:',
	'text' => 'Texte',
	'previoustext' => 'vorherigen Texte',
	'averterasebottomtext' => 'Cette opération effacera le texte du bas !',
	'averterasecurrenttext' => 'Cette opération effacera le texte en cours !',
	'averterasecurrentregexes' => 'Cette opération effacera les regex en cours !',
	'hidetextoforigin' => 'Cacher le texte d\'origine',
	'showtextoforigin' => 'Afficher le texte d\'origine',
	
	// Page 'Liste'
	'listtitle' => 'Liste des expressions régulières enregistrées',
	'listcss' => "
		.listePuces > li { margin-bottom:1.5em; }
		.phpcode { color:rgb(0, 119, 0); }
		.guill { color:rgb(221, 0, 0); }
		.regin, .regout { color:rgb(100, 0, 0); }",
	'noregexsaved' => 'Aucune expression régulière enregistrée.',
	'delete' => 'effacer',
	
	// Page 'Installation'
	'installtitle' => 'Installation de la base de données pour RegexMania',
	'nocreatedb' => 'Impossible de créer la base de données <tt>$1</tt>, mais peut-être la base existe-t-elle déjà et dans ce cas la table sera bien créée. Erreur renvoyée : <tt>$2</tt>.',
	'createdb' => 'Base de données <tt>$1</tt> créée.',
	'nocreatetable' => 'Impossible de créer la table <tt>$1</tt>. Erreur renvoyée : <tt>$2</tt>.',
	'createtable' => 'Table <tt>$1</tt> créée.',
	'endinstall' => 'Installation terminée. L\'enregistrement des expressions régulières devrait maintenant fonctionner.',
	'try' => 'Essayer !',
	'avertcreatedb' => 'Après cette étape sera créé la base de données relative au script RegexMania',
	'installnameserver' => 'Nom de l\'utilisateur du serveur MySQL :',
	'installpassserver' => 'Mot de passe de l\'utilisateur du serveur MySQL :',
	'installpasswww' => 'Mot de passe d\'administration :',
	'install' => 'Installer',
	
);
