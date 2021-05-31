<?php

interface intSecurize {
	static function session_token_drop ();
	static function session_token_add($token);
	static function session_token_retrieve() ;
	static function host_is_valid ($db, $calling_script='') ;
	static function generate_token($value = '') ;
	static function hmac_base64($data, $key) ;
	static function random_bytes($count);
	static function get_hash_salt();
	static function get_private_key();
	static function variable_get($name, $default = NULL);
	static function variable_set($name, $value);	
}

abstract class Securize implements intSecurize {

	public static function session_token_drop () {
		@session_start();
		unset($_SESSION['crud_token_form']) ;
		@session_write_close();
	}
	
	public static function session_token_add($token) {
		@session_start();
		$_SESSION['crud_token_form'] = $token ;
		@session_write_close();
	}
	
	public static function session_token_retrieve() {
		@session_start();
		$token = $_SESSION['crud_token_form'] ;
		@session_write_close();
		return $token ;
	}
	
	public static function host_is_valid ($db, $calling_script='') {
		$form_correct = true ;
		$http_referer = parse_url($_SERVER['HTTP_REFERER']) ;
		if ( $http_referer['scheme'] != 'http' && $http_referer['scheme'] != 'https' ) {
			error_log('FATAL ERROR : Formulaire compromis ('.$calling_script .'), protocole erroné ');
			$form_correct = false ;
		}
		if ($form_correct && !is_null($db)) {
			if ( $http_referer['host'] != $_SERVER['HTTP_HOST'] ) {
				if (isset($GLOBALS['active_auth_mode']) && $GLOBALS['active_auth_mode']===true ) {
					require_once 'model/SaxHostAuthModel.php' ;
					$hostmodel = new SaxHostAuthModel($db) ;
					if (! $hostmodel->check_by_hostname($http_referer['host']) ) {
						error_log('FATAL ERROR : Formulaire compromis ('.$calling_script .') utilisé en dehors de son site d\origine ');
						$form_correct = false ;
					}
				} else {
					error_log('FATAL ERROR : Formulaire compromis ('.$calling_script .') utilisé en dehors de son site d\origine ');
					$form_correct = false ;
				}
			}
		}
		return $form_correct ;
	}
	
	/**
	 * Generate a token based on $value, the current user session and private key.
	 *
	 * @param $value
	 * An additional value to base the token on.
	 */
	public static function generate_token($value = '') {
		if (trim($value) != '') {
			$value = date("YmdHis") ;
		}
		return self::hmac_base64 ( $value, session_id () . self::get_private_key () . self::get_hash_salt () );
	}
	
	/**
	 * Calculate a base-64 encoded, URL-safe sha-256 hmac.
	 *
	 * @param $data
	 * String to be validated with the hmac.
	 * @param $key
	 * A secret string key.
	 *
	 * @return
	 * A base-64 encoded sha-256 hmac, with + replaced with -, / with _ and
	 * any = padding characters removed.
	 */
	public static function hmac_base64($data, $key) {
		$hmac = base64_encode ( hash_hmac ( 'sha256', $data, $key, TRUE ) );
		// Modify the hmac so it's safe to use in URLs.
		return strtr ( $hmac, array ('+' => '-', '/' => '_', '=' => '' ) );
	}
	
	/**
	 * Returns a string of highly randomized bytes (over the full 8-bit range).
	 *
	 * This function is better than simply calling mt_rand() or any other built-in
	 * PHP function because it can return a long string of bytes (compared to < 4
	 * bytes normally from mt_rand()) and uses the best available pseudo-random source.
	 *
	 * @param $count
	 * The number of characters (bytes) to return in the string.
	 */
	public static function random_bytes($count) {
		// Initialize on the first call. The contents of $_SERVER includes a mix of
		// user-specific and system information that varies a little with each page.
		$random_state = print_r ( $_SERVER, TRUE );
		if (function_exists ( 'getmypid' )) {
			// Further initialize with the somewhat random PHP process ID.
			$random_state .= getmypid ();
		}
		$bytes = '';
		
		// this loop will
		// generate a good set of pseudo-random bytes on any system.
		// Note that it may be important that our $random_state is passed
		// through hash() prior to being rolled into $output, that the two hash()
		// invocations are different, and that the extra input into the first one -
		// the microtime() - is prepended rather than appended. This is to avoid
		// directly leaking $random_state via the $output stream, which could
		// allow for trivial prediction of further "random" numbers.
		while ( strlen ( $bytes ) < $count ) {
			$random_state = hash ( 'sha256', microtime () . mt_rand () . $random_state );
			$bytes .= hash ( 'sha256', mt_rand () . $random_state, TRUE );
		}
		
		$output = substr ( $bytes, 0, $count );
		$bytes = substr ( $bytes, $count );
		return $output;
	}

		
	/**
	 * Get a salt useful for hardening against SQL injection.
	 *
	 * @return
	 * A salt based on information in settings.php, not in the database.
	 */
	public static function get_hash_salt() {
		return self::variable_get('app_hash_salt') ;
	}
	
	/**
	 * Ensure the private key variable used to generate tokens is set.
	 *
	 * @return
	 * The private key.
	 */
	public static function get_private_key() {
		if (! ($key = self::variable_get ( 'app_private_key', 0 ))) {
			$key = self::random_bytes(55);
			self::variable_set ( 'app_private_key', $key );
		}
		return $key;
	}
	
	public static function variable_get($name, $default = NULL) {
		
		return isset ( $GLOBALS ['conf_app'][$name] ) ? $GLOBALS ['conf_app'][$name] : $default;
	}
	
	
	public static function variable_set($name, $value) {
	
		$GLOBALS ['conf_app'][$name] = $value;	  
	}
	
}
