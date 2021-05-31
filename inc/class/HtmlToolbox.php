<?php

abstract class HtmlToolbox {

    public static function getTableLineClass($even = true) {
        if ($even === true || $even === 1 || $even === '1') {
            return "even-row";
        } else {
            return "odd-row";
        }
    }

    public static function getReverseTableLineClass($even = true) {
        if ($even === true || $even === 1 || $even === '1') {
            return false;
        } else {
            return true;
        }
    }

    public static function back_link($libelle = '') {
        $libelle = trim($libelle);
        if ($libelle == '') {
            $libelle = 'Retour à la sélection';
        }
        //TODO : � tester
        // header('Location: '.$_SERVER['HTTP_REFERER'].');
        return '<a href="javascript:history.back();">' . $libelle . '</a><hr />';
    }

    public static function genHtmlLink($option, $titre, $externe = false) {
        if ($externe) {
            $target = 'target="_blank"';
        } else {
            $target = '';
        }
        return '<a href="' . $option . '" ' . $target . '>' . $titre . '</a>';
    }

    public static function genIconeLink($image, $action = '', $titre = '') {

        $titre = trim($titre);
        $action = trim($action);

        $link = '<img src="' . $image . '"';
        if ($titre != '') {
            $link .= ' alt="' . htmlentities($titre) . '"';
        }
        if ($action != '') {
            $link .= ' onclick="' . $action . ';return false;"';
        }
        $link .= ' />';

        return $link;
    }

    public static function genHrefLink($image, $action = '', $titre = '') {

        $titre = trim($titre);
        $action = trim($action);
        $link = '<a href="' . $action . '" class="modal">';
        $link .= '<img src="' . $image . '"';
        if ($titre != '') {
            $link .= ' alt="' . htmlentities($titre) . '"';
        }
        $link .= ' />';
        $link .= '</a>';

        return $link;
    }

    public static function genCrudIconeLink($option, $id, $profondeur = '', 
            $titre = '', $image = '') {
        $profondeur = self::genpath_profondeur($profondeur);

        $option = strtolower(trim($option));
        $id = trim($id);
        $titre = trim($titre);
        $action = '';
        $image = trim($image);

        switch ($option) {
            case 'upd': {
                    if ($titre == '') {
                        $titre = 'Mettre à jour';
                    }
                    if ($image == '') {
                        $image = $profondeur . 'images/b_edit.png';
                    }
                    $action = $option . '_item';
                    break;
                }
            case 'del': {
                    if ($titre == '') {
                        $titre = 'Supprimer';
                    }
                    if ($image == '') {
                        $image = $profondeur . 'images/b_drop.png';
                    }
                    $action = $option . '_item';
                    break;
                }
            case 'rtv': {
                    if ($titre == '') {
                        $titre = 'Afficher';
                    }
                    if ($image == '') {
                        $image = $profondeur . 'images/b_select.png';
                    }
                    $action = $option . '_item';
                    break;
                }
            case 'ins': {
                    if ($titre == '') {
                        $titre = 'Créer';
                    }
                    if ($image == '') {
                        $image = $profondeur . 'images/b_edit.png';
                    }
                    $action = $option . '_item';
                    break;
                }
            default : {
                    $option = '***';
                    $id = '';
                }
        }
        if ($action != '') {
            //$action .= '?crud_ope='.$option.'&crud_id='.$id ;
            $action .= "('" . $id . "')";
        }

        return self::genIconeLink($image, $action, $titre);
    }

    public static function genCrudLink($script_called, $option, $id, 
            $profondeur = '', $titre = '', $image = '') {
        $profondeur = self::genpath_profondeur($profondeur);

        $option = strtolower(trim($option));
        $id = trim($id);
        $titre = trim($titre);
        $action = '';
        $image = trim($image);
        $script_called = trim($script_called);

        switch ($option) {
            case 'upd': {
                    if ($titre == '') {
                        $titre = 'Mettre à jour';
                    }
                    if ($image == '') {
                        $image = $profondeur . 'images/b_edit.png';
                    }
                    $action = "{$script_called}?crud_ope={$option}&crud_id={$id}";
                    break;
                }
            case 'del': {
                    if ($titre == '') {
                        $titre = 'Supprimer';
                    }
                    if ($image == '') {
                        $image = $profondeur . 'images/b_drop.png';
                    }
                    $action = "{$script_called}?crud_ope={$option}&crud_id={$id}";
                    break;
                }
            case 'rtv': {
                    if ($titre == '') {
                        $titre = 'Afficher';
                    }
                    if ($image == '') {
                        $image = $profondeur . 'images/b_select.png';
                    }
                    $action = "{$script_called}?crud_ope={$option}&crud_id={$id}";
                    break;
                }
            case 'ins': {
                    if ($titre == '') {
                        $titre = 'Créer';
                    }
                    if ($image == '') {
                        $image = $profondeur . 'images/b_edit.png';
                    }
                    $action = "{$script_called}?crud_ope={$option}";
                    break;
                }
            default : {
                    $action = '#';
                    $option = '***';
                    $id = '';
                }
        }
        $action .= '&modal=true';
        return self::genHrefLink($image, $action, $titre);
    }

    public static function genLookupSelect($option, $libelle, 
            $profondeur = '', $titre = '', $image = '') {
        $profondeur = self::genpath_profondeur($profondeur);

        $option = trim($option);
        $libelle = trim($libelle);
        $titre = trim($titre);
        $action = '';
        $image = trim($image);

        if ($titre == '') {
            $titre = 'Sélectionner';
        }
        if ($image == '') {
            $image = $profondeur . 'images/check.gif';
        }
        $action = "getLookupRetrieve('$option', '$libelle')";

        return self::genIconeLink($image, $action, $titre);
    }

    public static function genTreeLink($option_menu, $profondeur = '') {
        if (isset($option_menu ['externe']) && $option_menu ['externe']) {
            $externe = true;
            $link_menu = $option_menu ['option'];
        } else {
            $externe = false;
            $link_menu = $profondeur . $option_menu ['option'];
        }
        return '<li>' . self::genHtmlLink($link_menu, $option_menu ['title'], 
                $externe) . '</li>' . PHP_EOL;
    }

    public static function genMenu($profondeur = '') {

        $profondeur = self::genpath_profondeur($profondeur);

        $liste_menus = MenuApp::initMenu();
        $menu = '<div id="div_navigation" >' . PHP_EOL;
        if (defined('TYP_MENU_APP') && TYP_MENU_APP == '*dropdown') {
            $menu .= '<ul class="dropdown">' . PHP_EOL;        
        } else {
            $menu .= '<ul id="menuapp">' . PHP_EOL;      
        }

        foreach ($liste_menus as $option_menu) {
            $top_affiche = false;
            // les options en "test" ne s'affichent que si on force l'URL avec le paramètre "test"
            if (isset($option_menu['test'])) {
                if (isset($GLOBALS['display_options_test']) 
                        && $GLOBALS['display_options_test'] === true) {
                    $top_affiche = true;
                } else {
                    if (isset($_GET['test']) && $option_menu['test']) {
                        $top_affiche = true;
                    }
                }
            } else {
                // options qui ne peuvent s'afficher que sur une plateforme particulière
                if (isset($option_menu['platform'])) {
                    if ($option_menu['platform'] == 'i5_only' && Misc::isIBMiPlatform()) {
                        $top_affiche = true;
                    }
                } else {
                    $top_affiche = true;
                }
            }
            if ($top_affiche) {
                $menu .= self::genSousMenu($option_menu, $profondeur);
            }
        }
        $menu .= '</ul>' . PHP_EOL;
        $menu .= '</div>' . PHP_EOL;
        return $menu;
    }

    public static function genSousMenu($option_menu, $profondeur) {

        if (defined('TYP_MENU_APP') && TYP_MENU_APP == '*dropdown') {
            $li = '<li class="dropdown_trigger">' ;        
        } else {
            $li = '<li>' ;      
        }

        $menu = '';
        if (is_array($option_menu) && is_array($option_menu['option'])) {
            $menu .= $li . $option_menu['title'] . PHP_EOL;
            $menu .= '<ul>' . PHP_EOL;
            foreach ($option_menu['option'] as $option_ss_menu) {
                if (is_array($option_ss_menu)) {
                    $menu .= self::genSousMenu($option_ss_menu, $profondeur);
                } else {
                    $menu .= self::genTreeLink($option_menu, $profondeur);
                }
            }
            $menu .= '</ul>' . PHP_EOL;
            $menu .= '</li>' . PHP_EOL;
        } else {
            $menu .= self::genTreeLink($option_menu, $profondeur);
        }
        return $menu;
    }

    public static function genpath_profondeur($profondeur) {
        if (is_int($profondeur)) {
            if ($profondeur > 0 && $profondeur < 10) {
                $profondeur = str_repeat('../', $profondeur);
            } else {
                $profondeur = '';
            }
        } else {
            $profondeur = trim($profondeur);
        }
        return $profondeur;
    }

    public static function entetePage($title = 'titre non défini', 
            $params = array(), $profondeur = '', $affiche_menu = true) {

        $profondeur = self::genpath_profondeur($profondeur);

        /*
         *  si une redirection a été demandée explicitement ('redirect_after'), ou 
         *  si une fonction d'export (CSV, XML ou SQL) a été demandée, alors on active la 
         *  bufférisation de la sortie pour pouvoir l'intercepter
         */
        if (array_key_exists('crud_export_csv', $_GET) 
                || array_key_exists('crud_export_csv', $_POST)
                || array_key_exists('crud_export_xml', $_GET) 
                || array_key_exists('crud_export_xml', $_POST)
                || array_key_exists('crud_export_sql', $_GET) 
                || array_key_exists('crud_export_sql', $_POST)
                || array_key_exists('redirect_after', $_POST)) {
            ob_start();
        }

        if (defined('TYP_APPLI') && TYP_APPLI == 'gregphplab') {
            if (defined('NOM_APPLI')) {
                $titre1 = NOM_APPLI ;
            } else {
                $titre1 = 'Le Labo du Défrichoir, côté "coulisses"' ;
            }
            $titre2 = '';
            $meta = <<<BLOC_META
<meta name="description" content="DBToolbox Le Défrichoir" />
<meta name="publisher" content="Le Défrichoir" />
<meta name="author" content="Le Défrichoir" />
BLOC_META;
        } else {
            $titre1 = 'Toolbox DB2 pour System i' ;
            $titre2 = NOM_APPLI . ' - ' . ucwords(strtolower(TYPE_ENVIR_APP)) .
                    ' (' . TYPE_ENVIR_EXE . ') - ' . $title;  
            $meta = <<<BLOC_META
<meta name="description" content="Le Défrichoir " />
<meta name="publisher" content="Le Défrichoir" />
<meta name="author" content="Le Défrichoir" />
<meta name="robots" content="noindex,nosnippet,nofollow,noarchive" />
BLOC_META;
        }
        /*
         * On n'affiche pas le menu en mode "CRUD"
         */
        $menu = '';
        $titre = '';
        if ($affiche_menu != false) {
            if (!array_key_exists('crud_ope', $_GET)) {
                $menu = self::genMenu($profondeur) . '<br/>';
            }

            if ($titre2 != '') {
            $titre = <<<BLOC_HTML
<div id="header">
<h2>{$titre1}</h2>
<h3>{$titre2}</h3>
</div>
BLOC_HTML;
            } else {
            $titre = <<<BLOC_HTML
<div id="header">
<h2>{$titre1}</h2>
</div>
BLOC_HTML;
                
            }
            $menu .= '<br/>';
        }

        $css_specif_code = '';
        $js_specif_code = '';
        $jquery_specif = '';
        $jquery_addlib = '';
        $jquery_css = '';

        /*
        $jquery_addlib .= '<script type="text/javascript" src="' . $profondeur .
                'js/modal.js"></script>' . PHP_EOL;
*/
        if (is_array($params) && count($params) > 0) {
            if (array_key_exists('js_specif_code', $params)) {
                $js_specif_code .= '<script language="javascript" type="text/javascript">' . PHP_EOL .
                        '//<![CDATA[' . PHP_EOL .
                        trim($params['js_specif_code']) . PHP_EOL .
                        '//]]>' . PHP_EOL .
                        '</script>' . PHP_EOL;
            }
            if (array_key_exists('css_specif_file', $params)) {
                $css_specif_code =
                        '<link rel="stylesheet" href="' . $profondeur . 
                        trim($params['css_specif_file']) . 
                        '" type="text/css" media="screen" />' . PHP_EOL;
            }
            if (array_key_exists('css_specif_code', $params)) {
                $jquery_css .= trim($params['css_specif_code']). PHP_EOL;
            }
            if (array_key_exists('jquery_addlib', $params)) {
                $jquery_specif .= rtrim($params['jquery_addlib']) . PHP_EOL;
            }
            if (array_key_exists('jquery_specif', $params)) {
                $jquery_specif .= rtrim($params['jquery_specif']) . PHP_EOL;
            }

            if (array_key_exists('jquery_datagrid', $params)) {
                $jquery_specif .= <<<BLOC_JS
    $('#datagrid').dataTable( {
        "oLanguage": {
            "sProcessing": "Traitement en cours...",
            "sLengthMenu": "Affiche _MENU_ entrées",
            "sZeroRecords": "Aucune donnée trouvée",
            "sEmptyTable": "Aucune donnée disponible pour cette table",
            "sLoadingRecords": "Chargement...",
            "sInfo": "Affichage _START_ à _END_ de _TOTAL_ entrées",
            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
            "sInfoFiltered": "(filtré sur un total de _MAX_ entrées)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sSearch": "Recherche:",
            "sUrl": "",
            "oPaginate": {
                    "sFirst":    "Premier",
                    "sPrevious": "Précédent",
                    "sNext":     "Suivant",
                    "sLast":     "Dernier"
            }	            
        }
    } );				
BLOC_JS;
                $jquery_specif .= PHP_EOL;
                $jquery_addlib .= '<script type="text/javascript" src="' . 
                        $profondeur . 'jsmedia/js/jquery.dataTables.js"></script>' .
                        PHP_EOL;
                $jquery_css .= <<<BLOC_CSS
<style type="text/css" title="currentStyle">
     @import "{$profondeur}jsmedia/css/demo_page.css";
     @import "{$profondeur}jsmedia/css/demo_table.css";
</style>
BLOC_CSS;
                $jquery_css .= PHP_EOL;
            }
        }

        if ($jquery_specif != '') {
            // encapsulation du code dans un bloc JS
            $jquery_specif = <<<BLOC_JQUERY
<script language="javascript" type="text/javascript">
//<![CDATA[
    window.onload= function(){ activateMenu('menuapp'); }
    $(document).ready(function() {  
{$jquery_specif}   
    })
//]]>
</script>
BLOC_JQUERY;
        }

        $bloc_jquery = <<<BLOC_JQUERY
<script type="text/javascript" src="{$profondeur}js/menudyn.js"></script>		
<link rel="stylesheet" href="{$profondeur}js/jqueryui/1.11.4/themes/excite-bike/jquery-ui.css" type="text/css" media="screen" />
{$jquery_css}
<script type="text/javascript" src="{$profondeur}js/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" src="{$profondeur}js/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="{$profondeur}js/js_specif.js"></script>
<script type="text/javascript" src="{$profondeur}js/jquery.validate.min.js"></script>
<script type="text/javascript" src="{$profondeur}js/jquery.validate.messages_fr.js"></script>
{$jquery_addlib}
{$jquery_specif}
BLOC_JQUERY;
        if (defined('DEFAULT_CHARSET')) {
            $encoding = DEFAULT_CHARSET;
        } else {
            $encoding = 'UTF-8';
        }

        if (defined('TYP_APPLI') && TYP_APPLI == 'gregphplab') {
            $css_comp = PHP_EOL.'<link rel="stylesheet" href="'.$profondeur.'screen_labo.css" type="text/css" media="all" />'.PHP_EOL;
        $titre = <<<BLOC_HTML
<fieldset class="ui-widget ui-widget-content ui-corner-all">
{$titre}
</fieldset>
BLOC_HTML;
        } else {
            $css_comp = PHP_EOL.'<link rel="stylesheet" href="'.$profondeur.'screen_std.css" type="text/css" media="all" />'.PHP_EOL;
        }
        
        
        
        $html = <<<BLOC_HTML
<!DOCTYPE html>
<html lang="fr-FR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$encoding}" />
<link rel="stylesheet" href="{$profondeur}screen.css" type="text/css" media="all" />{$css_comp}
<title>{$title}</title>
<link rel="icon" href="{$profondeur}images/favicon.ico" />
{$meta}{$bloc_jquery}{$js_specif_code}{$css_specif_code}
</head>
<body>
{$titre}
{$menu}
<div id="content">
BLOC_HTML;
        return $html . PHP_EOL;
    }

    public static function piedPage() {
        $startYear = 2018;
        $thisYear = date('Y');
        if ($startYear == $thisYear) {
            $year_display = $startYear;
        } else {
            $year_display = "{$startYear}-{$thisYear}";
        }

        $footer = <<<BLOC_HTML
&copy; Copyright {$year_display} <a href="http://ledefrichoir.com/" target="_blank">Le Défrichoir</a>
BLOC_HTML;
            
        $footer = <<<BLOC_HTML
<fieldset class="ui-widget ui-widget-content ui-corner-all">
{$footer}
</fieldset>
BLOC_HTML;
        
        $html = <<<BLOC_HTML
</div>		
<div id="footer" align="center">
{$footer}
</div>
</body>
</html>
BLOC_HTML;
        return $html . PHP_EOL;
    }

    public static function crudOrigineForm() {
        $html = <<<BLOC_HTML
<form id="crud_origine" name="crud_origine" method="get" action="" >
<input type="hidden" name="crud_callback_reload" id="crud_callback_reload" value="NO" onchange="reloadPage();return false;" />
<input type="hidden" name="crud_callback_id" id="crud_callback_id" value="" onchange="reloadPage();return false;" />
</form>
BLOC_HTML;
        return $html . PHP_EOL;
    }

    public static function crudJScalls($script_called, $window_pos = '') {
        $script_called = trim($script_called);
        $window_pos = trim($window_pos);
        if ($window_pos == '') {
            $window_pos = 'width=750,height=450,left=50,top=50';
        }
        $window_options = 'directories=yes,menubar=yes,status=no,location=no,scrollbars=yes,resizable=yes,fullscreen=yes,';
        $html = <<<BLOC_HTML
<script type="text/javascript">
function del_item(crud_id){
	LookupOpen("{$script_called}?crud_ope=del&amp;crud_id="+crud_id+"",'crud_delete','{$window_options}{$window_pos}');
        return false;
}
function upd_item(crud_id){
	LookupOpen("{$script_called}?crud_ope=upd&amp;crud_id="+crud_id+"",'crud_update','{$window_options}{$window_pos}');
        return false;
}
function rtv_item(crud_id){
	LookupOpen("{$script_called}?crud_ope=rtv&amp;crud_id="+crud_id+"",'crud_retrieve','{$window_options}{$window_pos}');
        return false;
}
function ins_item(){
	LookupOpen("{$script_called}?crud_ope=ins",'crud_insert','{$window_options}{$window_pos}');
        return false;
}
</script>
BLOC_HTML;
        return $html . PHP_EOL;
    }

}

