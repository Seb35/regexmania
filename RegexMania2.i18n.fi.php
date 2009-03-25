<?php

$messages = array(
	
	// Erreurs 'fatales'
	'error' => 'VIRHE :',
	'nodbuse' => 'RegexMania n\'est pas configuré pour enregistrer les expressions régulières.',
	'entername' => 'Veuillez donner un nom à la regex.',
	'enterpass' => 'Une regex avec ce nom existe déjà. Veuillez enter un autre nom ou entrer le mot de passe utilisé lors de l\'enregistrement.',
	'dbalreadyok' => 'La base de données est déjà prête à être utilisée',
	'nodbserverconnexion' => 'Impossible de se connecter au serveur MySQL. Veuillez vérifier les paramètres de la base de données dans le script (nom d\'hôte, nom d\'utilisateur et mot de passe associé).',
	'nodbconnexion' => 'Impossible de se connecter à la base de données.',
	
	// Écran principal
	'cribsheet' => 'muistio',
	'listregexes' => 'lista levytyksen',
	'compute' => 'Laskea',
	'save' => 'Kirjata',
	'name' => 'Nimi :',
	'comment' => 'Kommentti :',
	'password' => 'Salasana :',
	'protectregex' => 'suojella regex',
	'open' => 'Kytkeä',
	'total' => 'Määrä :',
	'erase' => 'Pestä pois :',
	'eraseallregexes' => 'kaikki regex',
	'erasetext' => 'teksti',
	'result' => 'Aiheutua :',
	'textoforigin' => 'Teksti alkuperä :',
	'text' => 'teksti',
	'previoustext' => 'entinen teksti',
	'averterasebottomtext' => 'Tämä teko pestää tekstit alas pois !',
	'averterasecurrenttext' => 'Tämä teko pestää nykyinen nykyiset tekstit pois !',
	'averterasecurrentregexes' => 'Tämä teko pestää nykyiset regex pois !',
	'hidetextoforigin' => 'Piilottaa teksti alkuperä',
	'showtextoforigin' => 'Näyttää teksti alkuperä',
	
	// Page 'Liste'
	'listtitle' => 'Lista säännöllisen lauseken kirjatanut',
	'listcss' => "
		.listePuces > li { margin-bottom:1.5em; }
		.phpcode { color:rgb(0, 119, 0); }
		.guill { color:rgb(221, 0, 0); }
		.regin, .regout { color:rgb(100, 0, 0); }",
	'noregexsaved' => 'Ei kukaan säännöllinen lauseke kirjatanut.',
	'delete' => 'pestä pois',
	
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
