<?php

$messages = array(
	
	// 'Fatal' errors
	'error' => 'ERROR:',
	'nodbuse' => 'RegexMania isn\'t configured to save regular expressions.',
	'entername' => 'Please enter a name for the regex.',
	'enterpass' => 'A regex with this name already exists. Please enter another name or enter the password used when saving.',
	'dbalreadyok' => 'The database is already ready for use.',
	'nodbserverconnexion' => 'Impossible to connect to the MySQL server. Please verify the database parameters in the script (host name, user name and corresponding password).',
	'nodbconnexion' => 'Impossible to connect to the database.',
	
	// Main screen
	'cribsheet' => 'crib sheet',
	'listregexes' => 'list of recordings',
	'compute' => 'Compute',
	'save' => 'Save',
	'name' => 'Name:',
	'comment' => 'Comment:',
	'password' => 'Password:',
	'protectregex' => 'protect regex',
	'open' => 'Open',
	'total' => 'Total:',
	'erase' => 'Erase:',
	'eraseallregexes' => 'all regexes',
	'erasetext' => 'text',
	'result' => 'Résult:',
	'textoforigin' => 'Text of origin:',
	'text' => 'text',
	'previoustext' => 'previous text',
	'averterasebottomtext' => 'This action will erase the bottom text!',
	'averterasecurrenttext' => 'This action will erase the current text!',
	'averterasecurrentregexes' => 'This action will erase current regexes!',
	'hidetextoforigin' => 'Hide the text of origin',
	'showtextoforigin' => 'Show the text of origin',
	
	// 'List' page
	'listtitle' => 'List of saved regular expressions',
	'listcss' => "
		.listePuces > li { margin-bottom:1.5em; }
		.phpcode { color:rgb(0, 119, 0); }
		.guill { color:rgb(221, 0, 0); }
		.regin, .regout { color:rgb(100, 0, 0); }",
	'noregexsaved' => 'No regular expression saved.',
	'delete' => 'delete',
	
	// 'Install' page
	'installtitle' => 'Installation of the database for RegexMania',
	'nocreatedb' => 'Impossible to create the database <tt>$1</tt>, but perhaps the database already exists and in this case the table will be created. Returned error: <tt>$2</tt>.',
	'createdb' => 'Database <tt>$1</tt> created.',
	'nocreatetable' => 'Impossible to connect to the table <tt>$1</tt>. Returned error: <tt>$2</tt>.',
	'createtable' => 'Table <tt>$1</tt> created.',
	'endinstall' => 'Installation complete. The saving of regular expressions should now work.',
	'try' => 'Try!',
	'avertcreatedb' => 'After this step will be created the database for the script RegexMania',
	'installnameserver' => 'Name of the MySQL server\'s user:',
	'installpassserver' => 'Password for this MySQL server\'s user:',
	'installpasswww' => 'Admin password:',
	'install' => 'Install',
	
);
