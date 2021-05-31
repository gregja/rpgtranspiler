<?php

abstract class Sanitize {
    /*
     * m�thode pour prot�ger les param�tres GET de diff�rents types d'attaques
     */

    public static function blinderGet($getchk, $san_type = "", $san_func = "", $sql_autorise = array()) {
        if (array_key_exists($getchk, $_GET)) {
            $tmp_get = $_GET[$getchk];
            $getclean = self::sanitizeVar($tmp_get, $san_type, $san_func);
        } else {
            $getclean = '';
        }
        return $getclean;
    }

    /*
     *  m�thode pour prot�ger les param�tres POST de diff�rents types d'attaques
     */

    public static function blinderPost($postchk, $san_type = "", $san_func = "", $sql_autorise = array()) {
        if (array_key_exists($postchk, $_POST)) {
            $tmp_post = $_POST[$postchk];
            $postclean = self::sanitizeVar($tmp_post, $san_type, $san_func);
        } else {
            $postclean = '';
        }
        return $postclean;
    }

    public static function sanitizeVar($san_var, $san_type = "", $san_func = "") {

        $san_var = trim(strip_tags($san_var));
        $san_var = trim(self::remove_invisible_characters($san_var));
        $tmp_var = '';
        $lng_var = strlen($san_var);
        /*
         *  Les cha�nes de plus de 50000 caract�res sont rejet�es syst�matiquement
         */
        if ($lng_var > 0 && $lng_var < 50000) {
            /*
             *  Convertit les caract�res sp�ciaux en entit�s HTML, sauf les quotes et double-quotes
             */
            $tmp_var = htmlspecialchars($san_var, ENT_NOQUOTES);

            /*
             *  force un type particulier si pr�cis� dans l'appel de la fonction
             */
            if ($san_type != '') {
                $arr_types = array("boolean", "bool", "integer", "int", "float", "string");
                if (in_array($san_type, $arr_types)) {
                    if (!settype($tmp_var, $san_type)) {
                        $tmp_var = '';
                    }
                } else {
                    // tentative d'utilisation d'un type inexistant, alors envoi d'un champ "vide" en r�ponse 
                    $tmp_var = '';
                }
            }

            /*
             *  applique une fonction particuli�re si pr�cis�e dans l'appel de la fonction
             */
            if ($san_func != '' && is_callable($san_func)) {
                /*
                 * apply functions to the variables, you can use the standard PHP
                 * functions, but also use your own for added flexibility.
                 */
                $tmp_var = $san_func($tmp_var);
            }
        }
        return $tmp_var;
    }

    /*
     * m�thode emprunt�e au framework Code Igniter
     */

    public static function remove_invisible_characters($str, $url_encoded = TRUE) {
        $non_displayables = array();

        // every control character except newline (dec 10)
        // carriage return (dec 13), and horizontal tab (dec 09)

        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

    /*
     * m�thode � appliquer uniquement sur les �crans de requ�tage permettant la saisie de code SQL explicite
     */

    public static function replace_sql_operators($str) {
        $tab_conversion = array(
            '*eq' => '=',
            '*EQ' => '=',
            '*ne' => '<>',
            '*NE' => '<>',
            '*lt' => '<',
            '*LT' => '<',
            '*gt' => '>',
            '*GT' => '>',
            '*le' => '<=',
            '*LE' => '<=',
            '*ge' => '>=',
            '*GE' => '>='
        );

        return str_replace(array_keys($tab_conversion), array_values($tab_conversion), $str);
    }

    /*
     * m�thode emprunt�e � l'article suivant :
     * http://www.sitepoint.com/character-encodings-and-input/
     * tr�s efficace pour �liminer certains caract�res d�clencheurs de plantages sur DB2
     */

    public static function remove_dangerous_characters($str) {
        return preg_replace('/[^\x09\x0A\x0D\x20-\x7F\xC0-\xFF]/', '', $str);
    }

    /*
     * Elimination de certains caract�res parasites, dont le caract�re SUB qui est renvoy� quelques fois par SQL,
     * notamment � la fin du source des proc�dures stock�es et des fonctions, quand elles sont r�cup�r�es via db2_connect
     * (le probl�me ne se pr�sentant pas avec PDO).
     * Le tableau des codes ASCII est disponible ici : www.asciitable.com
     */

    public static function clean_code($sql) {
        $tab_chr = array();
        for ($control = 0; $control < 32; $control++) {
            if ($control != 9 && $control != 10) {
                // les sauts de ligne et les tabulations sont conserv�es
                $tab_chr[] = chr($control);
            }
        }
        // le caract�re DEL est lui aussi � �liminer
        $tab_chr[] = chr(127);

        $sql = str_replace($tab_chr, '', $sql);

        return $sql;
    }

}
