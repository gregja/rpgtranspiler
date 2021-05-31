<?php
abstract class Misc {

	/**
	 * Renvoie "true" si le serveur est un IBM i, "false" dans le cas contraire
	 */
	public static function isIBMiPlatform () {
	
		return (php_uname('s') == 'OS400' || PHP_OS == "AIX" || PHP_OS == "OS400") ? true : false;
	
	}
	
	/**
	 * 
	 * @return number
	 */
	public static function getMicrotime() {
		list ( $usec, $sec ) = explode ( " ", microtime () );
		return (( float ) $usec + ( float ) $sec);
	}

	/**
	 * 
	 * @param unknown_type $start
	 */
	public static function getTimeDiff($start) {

		$stop = self::getMicrotime() ;
		return round ( ($stop - $start) * 1000, 2 );
	
	}
	
	/**
	 * technique basique pour identifier si le navigateur est IE ou pas
	 * (n�cessaire notamment pour r�gler des diff�rence d'affichage entre IE et Firefox)
	 */
	public static function isNavigatorIE () {
		$is_ie = strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE');
		if ($is_ie == false) {
			return false ;
		} else {
			return true ;
		}
	}
	
	public static function getHeaderEncoding($encoding='ISO-8859-1') {
		header("Content-Type: text/html;charset=".$encoding);
	}
	
	/**
	 * fonction basique destin�e � aider le d�veloppeur, et surtout destin�e � �voluer
	 * (pourrait dans l'avenir alimenter un fichier de log diff�rent de la log standard php)
	 * @param unknown_type $var
	 * @param boolean $affiche  (true = affiche � l'�cran via un var_dump format�)
	 */
	public static function assistDebug($var, $affiche=false) {
		// envoie syst�matique dans la log
		ob_start() ;
		var_dump($var);
		$dump = ob_get_clean() ;
		error_log($dump);
	
		// affichage dans le flux html si demand�
		if ($affiche) {
			echo '<pre>';
			var_dump($dump);
			echo '</pre>';
		}
	}
	
}