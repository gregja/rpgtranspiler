<?php

abstract class DateTools {

	/**
	 * convertit une date numérique (format SSAAMMJJ en date au format SQL)
	 * @param unknown_type $stDate
	 * @param unknown_type $controle_restrictif
	 */
	public static function datenum2sql($stDate, $controle_restrictif=true) {
        $date = null;
        if ($stDate != null && $stDate != 0) {
        	$stDate = strval($stDate);
            $date_day = substr($stDate,6,2);
            $date_month = substr($stDate,4,2);
            $date_year = substr($stDate,0,4);
            
            $date_ok = false;
            if ($controle_restrictif) {
            	if ($date_day > 0 && $date_day < 32) {
	            	if ($date_month > 0 && $date_month < 13) {
	            		$date_ok = true;
	            	}
            	} 
            } else {
            	$date_ok = true;
            }
            
			if ($date_ok) {
	            $date = date("Y-m-d", mktime(0, 0, 0,
	                    $date_month, $date_day, $date_year));
			}
        }
        return $date;
    }

	
}

