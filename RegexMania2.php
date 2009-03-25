<?php

/**
 * RegexMania
 * Auteur : Sébastien Beyou
 * Licence : Gnu General Public License
 * Mail : seb35wikipedia@gmail.com
 * Autre : http://fr.wikipedia.org/wiki/Utilisateur:Seb35
 */

// Init and parameters
$path = preg_replace( "/(.*)\.php5?$/iu", "$1", __FILE__ ); // or $_SERVER["SCRIPT_FILENAME"] or $_SERVER["PATH_TRANSLATED"] ?
$IP = $_SERVER["SCRIPT_NAME"];

require_once( "$path.parameters.php" );

function fatalError( $text, $time = 1700 ) {
	
	$time = $time ? " onload=\"setTimeout( 'history.back();', $time );\"" : "";
	$error = msg( 'error' );
	echo <<<EOT
<html>
<body$time>
$error $text
</body>
</html>
EOT;
	exit;
}

function msg( $key ) {
	
	global $messages;
	$msg = isset($messages[$key]) ? $messages[$key] : '???';
	$i = 1;
	
	$args = func_get_args();
	array_shift( $args );
	
	foreach( $args as $value ) {
		$msg = preg_replace( "/\\$$i/u", $value, $msg, -1 );
		$i++;
	}
	return $msg;
}



/**
 * Main object which manages the general execution
 */
class RegexMania {
	
	private $mAction;
	private $mText;
	private $mCountRegexes;
	private $mRegexes;
	private $mLang;
	private $mRequest;
	private $mDB;
	private $mOutput;
	
	function __construct() {
		
		global $path, $language, $messages;
		
		// Values
		$this->mRequest = new Request();
		
		// Action
		$this->mAction = $this->mRequest->getAction();
		
		// Language
		$this->mLang = $this->mRequest->getVal( 'lang', $language );
		if( file_exists( "$path.i18n.$this->mLang.php" ) ) require_once( "$path.i18n.$this->mLang.php" );
		else if( file_exists( "$path.i18n.$language.php" ) ) {
			require_once( "$path.i18n.$language.php" );
			$this->mLang = $language;
		}
		else {
			require_once( "$path.i18n.fr.php" );
			$this->mLang = 'fr';
		}
		
		// Text
		$this->mText = $this->mRequest->getVal( 'texte' );
		
		// Regexes
		$this->mRegexes = array();
		$this->mCountRegexes = $this->mRequest->getVal('nbreg');
		
		for( $i=1; $i<=$this->mCountRegexes; $i++ ) {
			
			$this->mRegexes[] = array( $this->mRequest->getVal("regin$i"), $this->mRequest->getVal("regmod$"), $this->mRequest->getVal("regout$i") );
		}
		
		for( $i=$this->mCountRegexes+1; $i<=1000; $i++ ) {
			if( $this->mRequest->getVal("regin$i") || $this->mRequest->getVal("regmod$i") || $this->mRequest->getVal("regout$i") ) {
				$this->mRegexes[] = array( $this->mRequest->getVal("regin$i"), $this->mRequest->getVal("regmod$"), $this->mRequest->getVal("regout$i") );
			}
			
			$j = $i+1;
			if( !$this->mRequest->getVal("regin$j") && !$this->mRequest->getVal("regmod$j") && !$this->mRequest->getVal("regout$j") ) break;
			
		}
		
		// Database
		$this->mDB = new DB();
		if( $this->mDB->isOK() === false && in_array( $this->mAction, array('save', 'open', 'liste', 'delete') ) ) fatalError( msg('nodbuse') );
		
		// Output
		$this->mOutput = new Output();
		
	}
	
	/**
	 * Execute the requested action
	 */
	function execute() {
		
		switch( $this->mAction ) {
			case 'help':
				$this->Help();
				break;
			
			case 'compute':
				$this->Compute();
				break;
			
			case 'liste':
				$this->Liste();
				$this->mOutput->display();
				break;
			
			case 'open':
				$this->Open();
				break;
			
			case 'save':
				$this->Save();
				break;
			
			case 'delete':
				$this->Delete();
				$this->mOutput->display();
				break;
			
			case 'install':
				$this->Install();
				$this->mOutput->display();
				break;
			
			default:
				$this->mOutput->start( $this->mDB->isOK(), $this->mLang, $this->mText, array(), '', $this->mCountRegexes, $this->mRegexes );
				break;
		}
	}
	
	/**
	 * Display the Help page
	 */
	function Help() {
		
		global $path, $backgroundColor, $IP, $language;
		if( file_exists( "$path.help.$language.ihtml" ) ) include_once( "$path.help.$language.ihtml" );
		else include_once( "$path.help.fr.ihtml" );
	}
	
	/**
	 * The most important action: compute the result
	 */
	function Compute() {
		
		$nbdereg = $this->mRequest->getVal('nbreg');
		
		$regs = array();
		for( $i=0; $i<$nbdereg; $i++ ) {
			$regs[$i] = new Reg( $this->mRequest->getVal('regin'.($i+1)), $this->mRequest->getVal('regmod'.($i+1)), $this->mRequest->getVal('regout'.($i+1)) );
		}
		
		$out = $this->mText;
		for($i=0; $i<$nbdereg; $i++) $out = $regs[$i]->execute($out);
		
		$this->mOutput->start( $this->mDB->isOK(), $this->mLang, $this->mText, $regs, $out );
	}
	
	/**
	 * Display the list of saved regexes
	 */
	function Liste() {
		
		global $IP;
		
		$title = msg('listtitle');
		$css = msg('listcss');
		$head = <<<EOT
	<title>$title</title>
	<style type="text/css">
$css
	</style>
EOT;
		$this->mOutput->addHead( $head );
		$this->mOutput->addBody( "<h2>$title</h2><br />\n" );
		
		$regexes = $this->mDB->listReg();
		
		if( $regexes === false ) {
			$this->mOutput->addBody( '<i>'.msg('noregexsaved').'</i>' );
			return;
		}
		
		$this->mOutput->addUl( true, '', 'listePuces' );
		foreach( $regexes as $regex ) {
			
			list( $regName, $regComment, $regProtected, $regReg ) = $regex;
			
			if( $regComment ) $this->mOutput->addBody( "<li>\n<b>$regName</b> (<i>$regComment</i>) (<a href=\"$IP?action=delete&title=$regName\">".msg('delete')."</a>) :\n" );
			else $this->mOutput->addBody( "<li>\n<b>$regName</b> (<a href=\"$IP?action=delete&title=$regName\">".msg('delete')."</a>) :\n" );
			
			$this->mOutput->addUl( true );
			foreach( $regReg as $reg ) {
				$reg = new Reg( $reg[0], $reg[1], $reg[2] );
				$this->mOutput->addLi( $reg->display('', false) );
			}
			$this->mOutput->addUl( false );
		}
		$this->mOutput->addUl( false );
	}
	
	/**
	 * Open a regex
	 */
	function Open() {
		
		$openName = $this->mRequest->getVal('nameOpen');
		
		list( $openName, $openComment, $openProtect, $openReg ) = $this->mDB->openReg( $openName, 'ORDER BY regid' );
		
		$this->mOutput->start( $this->mDB->isOK(), $this->mLang, $this->mText, array(), '', count($openReg), $openReg, $openName, $openComment );
	}
	
	/**
	 * Save a regex
	 */
	function Save() {
		
		global $dbTable, $motDePasse;
		
		//Initialisation
		$magicQuotes = get_magic_quotes_gpc() ? true : false;
		$savingName = $this->mRequest->getVal('nameSave');
		$admin = 0;
		if( $this->mRequest->getVal('protectSave') && addslashes( $this->mRequest->getVal('passwordSave') ) == $motDePasse ) $admin = 1;
		if(!$savingName) fatalError( msg('entername') );
		
		//Vérification pour ne pas écraser de fichier sans droits
		$answerExists = $this->mDB->openReg( $savingName );
		if( $answerExists !== false ) {
			if( !$admin && $motDePasse ) fatalError( msg('enterpass') );
			else $this->mDB->delReg( $savingName );
		}
		
		//Enregistrement
		for($i=0; $i<$this->mRequest->getVal('nbreg'); $i++) {
			if($magicQuotes) mysql_query("INSERT INTO $dbTable VALUES('', '$savingName', '".$this->mRequest->getVal('commentSave')."', '$admin', '$i', '".$this->mRequest->getVal('regin'.($i+1))."', '".$this->mRequest->getVal('regmod'.($i+1))."', '".$this->mRequest->getVal('regout'.($i+1))."')");
			else mysql_query("INSERT INTO $dbTable VALUES('', '".addslashes($savingName)."', '".addslashes($this->mRequest->getVal('commentSave'))."', '$admin', '$i', '".addslashes($this->mRequest->getVal('regin'.($i+1)))."', '".addslashes($this->mRequest->getVal('regmod'.($i+1)))."', '".addslashes($this->mRequest->getVal('regout'.($i+1)))."')");
		}
		
		$this->mOutput->start( $this->mDB->isOK() );
	}
	
	/**
	 * Delete a regex
	 */
	function Delete() {
		
		global $motDePasse, $IP;
		
		$fichier = addslashes( $this->mRequest->getVal('title') );
		$pass = addslashes( $this->mRequest->getVal('passwordDelete') );
		if( (!$pass || $pass != $motDePasse) && $motDePasse )
		{
			$this->mOutput->addBody( "<h3>".msg('delete')." $fichier</h3><br />\n" );
			$this->mOutput->addBody( "<form action=\"$IP?action=delete&title=$fichier\" method=\"post\">" );
			$this->mOutput->addBody( '<input type="text" name="passwordDelete" size="11" /><input type="submit" value="'.msg('delete').' />' );
			$this->mOutput->addBody( "</form>" );
		}
		if( $pass == $motDePasse || !$motDePasse )
		{
			$this->mDB->delReg( $fichier );
			$this->Liste();
		}
	}
	
	/**
	 * Install the database backend
	 */
	function Install() {
		
		global $useDB, $IP, $dbUser, $dbPassword, $dbName, $dbTable, $motDePasse;
		
		if( !$useDB ) {
			$this->mOutput->start( $this->mDB->isOK() );
			return;
		}
		if( $this->mDB->isOK() ) fatalError( msg('dbalreadyok') );
		
		$pass = addslashes( $this->mRequest->getVal('passwordInstall') );
		$nam = addslashes( $this->mRequest->getVal('nameInstall') );
		$pass2 = addslashes( $this->mRequest->getVal('passwordInstall2') );
		
		if($pass == $dbPassword && $nam == $dbUser && $pass2 == $motDePasse) {
			
			// Is the MySQL connexion ok ?
			if( $this->mDB->state() < 1 ) fatalError( msg('nodbserverconnexion'), false);
			$this->mOutput->addBody( '<h2>'.msg('installtitle')."</h2>\n" );
			$this->mOutput->addUl( true );
			
			// Creation of the database
			$error = '';
			if( !($error = $this->mDB->createDB()) ) $this->mOutput->addLi( msg('nocreatedb', $dbTable, $error) );
			else $this->mOutput->addLi( msg('createdb', $dbName) );
			
			// Creation of the table
			$error = '';
			if( $this->mDB->state() < 2 ) fatalError( msg('nodbconnexion') );
			if( !($error = $this->mDB->createTable()) ) $this->mOutput->addLi( msg('nocreatetable', $dbTable, $error) );
			echo $this->mOutput->addLi( msg('createtable', $dbTable) );
			
			// End
			$this->mOutput->addUl( false );
			$this->mOutput->addBody( msg('endinstall')."<br />\n<a href=\"$IP\" target=\"_blank\">".msg('try')."</a>" );
		}
		else {
			$this->mOutput->addBody( '<h2>'.msg('installtitle')."</h2>\n" );
			$this->mOutput->addBody( "<form action=\"$IP?action=install\" method=\"post\">" );
			$this->mOutput->addBody( msg('avertcreatedb').'<br />' );
			$this->mOutput->addBody( msg('installnameserver')." <input type=\"text\" name=\"nameInstall\" /><br />\n" );
			$this->mOutput->addBody( msg('installpassserver')." <input type=\"text\" name=\"passwordInstall\" /><br />\n" );
			$this->mOutput->addBody( msg('installpasswww')." <input type=\"text\" name=\"passwordInstall2\" /><br />\n" );
			$this->mOutput->addBody( "<input type=\"submit\" value=\"".msg('install')."\" />\n" );
			$this->mOutput->addBody( "</form>" );
		}
	}
}



/**
 * This object is the interface to get values
 */
class Request {
	
	private $mMagicQuotes;
	private $mAction;
	
	/**
	 * Construct the Request object
	 */
	function __construct() {
		
		$action = $this->getVal( 'action' );
		
		     if( $this->getVal('wpCompute') ) $this->mAction = 'compute';
		else if( $this->getVal('wpSave')    ) $this->mAction = 'save';
		else if( $this->getVal('wpOpen')    ) $this->mAction = 'open';
		else if( $action == 'aide'          ) $this->mAction = 'help';
		else if( $action == 'liste'         ) $this->mAction = 'liste';
		else if( $action == 'delete'        ) $this->mAction = 'delete';
		else if( $action == 'install'       ) $this->mAction = 'install';
		else $this->mAction = 'start';
		
		$this->mMagicQuotes = get_magic_quotes_gpc() ? true : false;
	}
	
	/**
	 * Return the user-requested action
	 * 
	 * @return text
	 */
	function getAction() {
		
		return $this->mAction;
	}
	
	/**
	 * Get a value from $_REQUEST
	 * 
	 * @param $name text: key
	 * @param $default mixed: default value if the key is not defined
	 * @return mixed
	 */
	function getVal( $name, $default = '' ) {
		
		if( isset( $_REQUEST[$name] ) ) return $this->unquote( $_REQUEST[$name] );
		else return $this->unquote( $default );
	}
	
	/**
	 * Text without protected quotes
	 * 
	 * @param $texte text: text to unquote
	 * @return text
	 */
	function unquote( $texte ) {
		
		if( $this->mMagicQuotes ) return preg_replace(array("%\\\'%u", '%\\\"%u', '%\\\\\\\\%u'), array("'", '"', '\\'), $texte);
		else return $texte;
	}
	
}


/**
 * Database class: essentially open the connexion
 */
class DB {
	
	private $table;
	private $ok;
	private $state;
	
	/**
	 * Initialise the MySQL connection when constructing
	 */
	function __construct() {
		
		global $useDB, $dbTable;
		
		$this->ok = false;
		if( !$useDB ) return;
		
		$this->table = $dbTable;
		$this->state = 0;
		$this->connect();
	}
	
	/**
	 * Close the MySQL connection when destructing
	 */
	function __destruct() {
		
		if( $this->isOK() ) mysql_close();
	}
	
	/**
	 * Connect to the database
	 * 
	 * @return boolean
	 */
	private function connect() {
		
		global $dbHost, $dbUser, $dbPassword, $dbName;
		
		if( $this->state < 1 ) {
			if( !mysql_connect( $dbHost, $dbUser, $dbPassword ) ) return 0;
			$this->state = 1;
		}
		
		if( $this->state < 2 ) {
			if( !mysql_select_db( $dbName ) ) return 1;
			$this->state = 2;
		}
		
		if( $this->state < 3 ) {
			if( mysql_query( "SELECT DISTINCT regname, regcomment FROM $this->table ORDER BY regname" ) === false ) return 2;
			$this->state = 3;
		}
		
		$this->ok = true;
		
		return 3;
	}
	
	/**
	 * Return true if the database works
	 * 
	 * return boolean
	 */
	function isOK() {
		
		return $this->ok;
	}
	
	/**
	 * Return the connexion state \in {0,1,2,3}
	 * 
	 * @return integer
	 */
	function state() {
		
		return $this->state;
	}
	
	/**
	 * Gives all regexes
	 * 
	 * @return array
	 */
	function listReg() {
		
		if( !$this->isOK() ) return false;
		
		$result = mysql_query( "SELECT DISTINCT regname, regcomment FROM $this->table ORDER BY regname" );
		
		if( !$result || mysql_num_rows($result) == 0 ) return false;
		
		$listReg = array();
		while( $donnees = mysql_fetch_array($result) ) $listReg[] = $this->openReg( $regName = $donnees['regname'], 'ORDER BY regid' );
		
		return $listReg;
	}
	
	/**
	 * Open a regex
	 * 
	 * @param $name text: name of the regex
	 * @param $clauses text: options in the query
	 * @return ressource
	 */
	function openReg( $name, $clauses = '' ) {
		
		if( !$this->isOK() ) return false;
		
		$result = mysql_query( "SELECT * FROM $this->table WHERE regname='$name' $clauses" );
		
		if( !$result ) return false;
		
		// Création d'une cellule regex remplie avec les données puis création d'une dernière cellule vide (si n>=2)
		$openComment = '';
		$openProtected = false;
		$openReg = array();
		while( $donnees = mysql_fetch_array($result) ) {
			$openComment = $donnees['regcomment'];
			$openProtect = $donnees['regprotected'] ? true : false;
			$openReg[] = array( $donnees['regin'], $donnees['regmod'], $donnees['regout'] );
		}
		
		return array( $name, $openComment, $openProtected, $openReg );
		
		return $result;
	}
	
	/**
	 * Save a regex
	 * 
	 * @param $name text: name of the regex
	 * @param $comment text: comment of the regex
	 * @param $protected boolean: true to protect in writing the regex
	 * @param $regexes array: the regexes themself
	 * @return boolean
	 */
	function saveReg( $name, $comment, $protected, $regexes ) {
		
		$magicQuotes = get_magic_quotes_gpc() ? true : false;
		
		if( $protected ) $protected = 1;
		else $protected = 0;
		
		for( $i=0; $i<count($regexes); $i++ ) {
			
			if( $magicQuotes ) $r = mysql_query( "INSERT INTO $this->table VALUES('', '$savingName', '$comment', '$protected', '$i', '".$regexes[$i][0]."', '".$regexes[$i][1]."', '".$regexes[$i][2]."')" );
			
			else $r = mysql_query( "INSERT INTO $this->table VALUES('', '".addslashes($savingName)."', '".addslashes($comment)."', '$protected', '$i', '".addslashes($regexes[$i][0])."', '".addslashes($regexes[$i][1])."', '".addslashes($regexes[$i][2])."')" );
			
			if( !$r ) return false;
		}
	}
	
	/**
	 * Delete a regex
	 * 
	 * @param $name text: name of the regex
	 */
	function delReg( $name ) {
		
		if( !$this->isOK() ) return false;
		
		return mysql_query( "DELETE FROM $this->table WHERE regname='$name'" );
	}
	
	/**
	 * Create a database if it don't exists
	 * 
	 * @return boolean
	 */
	function createDB() {
		
		if( $this->state != 1 ) return false;
		
		global $dbName;
		$r = mysql_query( "CREATE DATABASE `$dbName` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;" );
		$this->connect();
		
		return $r;
	}
	
	/**
	 * Create the table
	 * 
	 * @return boolean
	 */
	function createTable() {
		
		if( $this->state != 2 ) return false;
		
		$r = mysql_query( "CREATE TABLE `$this->table` (
			`id` INT( 8 ) NOT NULL AUTO_INCREMENT ,
			`regname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL ,
			`regcomment` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL ,
			`regprotected` INT( 1 ) DEFAULT '0',
			`regid` INT( 2 ) UNSIGNED DEFAULT NULL ,
			`regin` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL ,
			`regmod` VARCHAR( 19 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL ,
			`regout` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL ,
			PRIMARY KEY ( `id` ) ,
			INDEX ( `regname` ) 
			) TYPE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Table du script RegexMania';" );
		$this->connect();
		
		return $r;
	}
}



/**
 * Class representing a regex
 */
class Reg {
	
	var $reg1;
	var $reg2;
	var $nbfait;
	
	function __construct( $regin, $regmod, $regout ) {
		
		$this->reg1 = "/$regin/u$regmod";
		$this->reg2 = $regout;
		
		$version = explode('.', PHP_VERSION);
		if((int)$version > 5 || ((int)$version[0] == 5 && (int)$version[1] >= 1)) $this->nbfait = 0;
		else $this->nbfait = false;
	}
	
	function execute( $text ) {
		
		if( $this->nbfait === false ) return preg_replace( $this->reg1, $this->reg2, $text, -1 );
		else return preg_replace( $this->reg1, $this->reg2, $text, -1, $this->nbfait );
	}
	
	function display( $text = '', $shownbfait = true ) {
		return '<span class="phpcode">preg_replace(</span>'
.'<span class="guill">"</span>'
.'<span class="regin">'.htmlentities(preg_replace(array("/\n/u", '%"%u'), array('\n', '\\"'), $this->getRegIn()), ENT_NOQUOTES, 'UTF-8').'</span>'
.'<span class="guill">"</span>'
.'<span class="phpcode">, </span>'
.'<span class="guill">"</span>'
 .'<span class="regout">'.htmlentities(preg_replace(array("/\n/u", '%"%u'), array('\n', '\\"'), $this->getRegOut()), ENT_NOQUOTES, 'UTF-8').'</span>'
.'<span class="guill">"</span>'
.$text
.'<span class="phpcode">);</span>'
.($this->nbfait === true && $shownbfait ? ' &#x2192; '.$this->nbfait.' remplacements' : '');
		/*return 'preg_replace("'.preg_replace(array("/
/u", ($isFree ? '/\\\\\\\\/u' : '/\\\\/u')), array('&#x5C;n', '&#x5C;'), htmlentities($this->getRegIn(), ENT_QUOTES, 'UTF-8')).'", "'.preg_replace(array("/
/u", ($isFree ? '/\\\\\\\\/u' : '/\\\\/u')), array('&#x5C;n', '&#x5C;'), htmlentities($this->getRegOut(), ENT_QUOTES, 'UTF-8')).'", '.$text.');'.($isFree ? '': ' &#x2192; '.$this->nbfait.' remplacements');*/
	}
	
	function getRegIn() { return $this->reg1; }
	function getRegOut() { return $this->reg2; }
	
}



class Output {
	
	private $mHead;
	private $mBody;
	
	function __construct() {
		
		$this->mHead = '';
		$this->mBody = '';
	}
	
	
	function addHead( $head ) {
		
		$this->mHead .= $head;
	}
	
	
	function addBody( $body ) {
		
		$this->mBody .= $body;
	}
	
	
	function addUl( $open = true, $name = '', $class = '' ) {
		
		if( $name ) $name = " name=\"$name\"";
		if( $class ) $class = " class=\"$class\"";
		
		if( $open ) $this->addBody( "<ul$name$class>\n" );
		else $this->addBody( "</ul>\n" );
	}
	
	
	function addLi( $content ) {
		
		$this->addBody( "<li>$content</li>\n" );
	}
	
	
	// Unité d'une regex (ensemble regex d'entrée, modificateurs, regex de sortie, bouton 'valider')
	function unitTab($i, $display=false, $regin='', $regmod='', $regout='', $miseEnForme=true) { // $i > 0
		
		$display = $display ? '' : ' style="display:none;"';
		$chaine = <<<EOT
<div class="regedit" id="tab$i"$display>
	<table style="width:100%;">
		<tr valign="middle" style="width:100%;">
			<td><span style="font-size:50pt;">"/</span></td>
			<td style="width:100%;"><textarea cols="50" rows="5" name="regin$i" id="regin$i" style="width:100%;">$regin</textarea></td>
			<td><span style="font-size:50pt;">/</span></td>
			<td><input type="text" size="5" name="regmod$i" id="regmod$i" value="$regmod" style="font-size:12pt;" /></td>
			<td><span style="font-size:50pt;">"</span></td>
		</tr>
	<tr valign="middle" style="width:100%;">
			<td><span style="font-size:50pt;">&nbsp;"</span></td>
			<td style="width:100%;"><textarea cols="50" rows="5" name="regout$i" id="regout$i" style="width:100%;">$regout</textarea></td>
			<td><span style="font-size:50pt;">"</span></td>
		</tr>
	</table>
</div>
EOT;
		if($miseEnForme) return $chaine;
		else return preg_replace('/\>\s+\</u', '><', $chaine);
	}
	
	
	function start( $db = false, $langue = 'fr', $texte = '', $regAnswer = array(), $result = '', $countRegOpen = 0, $regOpen = array(), $openingName = '', $openingComment = '' ) {
		
		global $path, $IP, $motDePasse, $backgroundColor, $languages;
		
		// Code HTML à rajouter par le JavaScript
		$ut = $this->unitTab('\' + (longueur+1) + \'', false, '', '', '', false);
		
		// Affichage du résultat
		$htmlResult = '';
		foreach( $regAnswer as $i => $reg ) {
			if( $i == 0 ) $t = ', $'.msg('text');
			else $t = ', $'.msg('previoustext');
			$htmlResult .= '    '.$reg->display( $t )."<br />\n";
		}
		
		// Remplissage lors de l'ouverture
		$htmlRegOpen = '';
		if( count( $regOpen ) > 0 ) {
			for( $i=0; $i<count($regOpen); $i++ ) {
				$htmlRegOpen .= '   ' . $this->unitTab( $i+1, $i<$countRegOpen, $regOpen[$i][0], $regOpen[$i][1], $regOpen[$i][2] );
			}
			$htmlRegOpen .= '   ' . $this->unitTab( count($regOpen)+1, false, '', '', '' );
		}
		else {
			$htmlRegOpen .= '   ' . $this->unitTab( 1, true, '', '', '' );
			$htmlRegOpen .= '   ' . $this->unitTab( 2, false, '', '', '' );
		}
		
		// Textes
		if( $result ) {
			$textResult = $result;
			$textOrigin = $texte;
			$textNew = '';
		}
		else {
			$textResult = '';
			$textOrigin = '';
			$textNew = $texte;
		}
		
		// Compteur
		$htmlOption = '';
		for( $i=1; $i<=count($regOpen)+1 || $i<3; $i++ ) {
			$selected = '';
			if( $i == $countRegOpen ) $selected = ' selected="selected"';
			$htmlOption .= "        <option value=\"$i\"$selected>$i</option>\n";
		}
		
		// Languages
		$htmlLanguages = '';
		foreach( $languages as $lang ) {
			$selected = '';
			if( $lang == $langue ) $selected = ' selected="selected"';
			$htmlLanguages .= "        <option value=\"$lang\"$selected>$lang</option>\n";
		}
		
		include( "$path.main.ihtml" );
		
	}
	
	
	function display() {
		
		global $backgroundColor;
		
		$head = $this->mHead;
		$body = $this->mBody;
		
		echo <<<EOT
<html>
<head>
$head
</head>
<body style="background:$backgroundColor;">
$body
</body>
</html>
EOT;
	}
	
}

///////////////
// Execution //
///////////////

$regexmania = new RegexMania();

$regexmania->execute();

