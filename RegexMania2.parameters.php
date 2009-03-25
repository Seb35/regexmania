<?php

/**
 * General parameters
 */

// String: background color used for all screens
$backgroundColor = '#d2d7e8';

// String: password used on the www interface to delete regexes or change a protected regex
$motDePasse = 'a';

// String: default language of the interface (ISO 639)
$language = 'fi';

// Array of strings: lists of available languages
$languages = array( 'de', 'en', 'fr', 'fi', 'ru' );

/**
 * Database
 */

// Boolean: general parameter to autorize the use of a (MySQL) database
$useDB = true;

// String: host of the MySQL server
$dbHost = 'localhost';

// String: user to the MySQL server (SELECT, INSERT, DELETE for the specific table; CREATE for installation)
$dbUser = 'seb';

// String: password corresponding to the MySQL user
$dbPassword = 'kkr';

// String: name of the database
$dbName = 'regexmania';

// String: name of the table (one only table is required)
$dbTable = 'regexmania';
//$dbTable = 'essaireg2';
