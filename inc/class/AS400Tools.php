<?php
abstract class AS400Tools {
	
	public static function get_typeAttributes() {
		$types = array ('*ALL', 'CLE', 'CLLE', 'CLP', 'DSPF', 'LF', 'PF', 'PROMPT', 'PRTF', 'QRY', 'RPG', 'RPGLE', 'SQL' );
		$types2 = array();
		foreach ($types as $values) {
			$types2[$values] = $values;
		}
		return $types2 ;
	}
	
	public static function get_typeObjects() {
		$types = array ('*ALL', '*PGM', '*SRVPGM', '*MODULE', '*MSGF', '*FILE', '*JOBD', '*CMD', '*DTAARA', '*QRYDFN', '*OVL', '*QMQRY', '*JRNRCV', '*JRN' );
		$types2 = array();
		foreach ($types as $values) {
			$types2[$values] = $values;
		}
		return $types2 ;
		
	}

	public static function get_typeUserClasses() {
		$types = array ( '*ALL', '*USER' , '*PGMR', '*SYSOPR', '*SECOFR'  );
		$types2 = array();
		foreach ($types as $values) {
			$types2[$values] = $values;
		}
		return $types2 ;		
	}
	
	public static function get_typeUserStatus() {
		$types = array ( '*ALL', '*ENABLED' , '*DISABLED'  );
		$types2 = array();
		foreach ($types as $values) {
			$types2[$values] = $values;
		}
		return $types2 ;		
	}
	
	
}
