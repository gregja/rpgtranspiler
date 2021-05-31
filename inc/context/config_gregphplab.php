<?php 

/*
 * profil de connexion DB2
 */
$usr = 'nodef';
$pwd = 'nodef';

/*
 * Titre de l'application
 */
define ( 'NOM_APPLI', 'Labo PHP/RPG' );
define ( 'TYP_APPLI', 'gregphplab') ;

/*
 * liste bibliothèques par environnement
 */
$liste_bibs = array ();

$zend_server_type = 'LAMP' ; // environnement d'exécution 

define ( 'TYPE_ENVIR_EXE', php_uname ( 's' ) );
define ( 'TYPE_ENVIR_APP', '' );
define ( 'TYPE_ENVIR_APP02', '' ); 
define ( 'TYPE_ENVIR_APP03', '' );

define ( 'BIB_REF_DTA', '' ); // Bib de référence pour le stockage des "traces" notamment
define ( 'BIB_REF_PGM', '' ); // Bib de référence où sont stockées les procécures stockées


