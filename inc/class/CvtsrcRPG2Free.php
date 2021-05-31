<?php

require_once 'Cvtsrc.php';

class CvtsrcRPG2Free extends Cvtsrc implements interfaceCvtsrc {

    public function __construct($filename, $component_name, $type_component, $params = array()) {
        $this->conditions = array(
            'EQ' => '=', 
            'NE' => '<>', 
            'LT' => '<', 
            'GT' => '>', 
            'LE' => '<=', 
            'GE' => '>=');
        $this->condjoint = array('OR' => 'or', 'AND' => 'and');
        $this->opermaths = array(
            'ADD' => '+', 
            'SUB' => '-', 
            'MULT' => '*', 
            'DIV' => '/');
        $this->version_code = '1.0';
        $this->version_date = '2018-01-01';
        $this->max_src_lines = 40000 ; 
        
        /*
         * 
          CL0N01Factor1+++++++Opcode&ExtFactor2+++++++Result++++++++Len++D+HiLoEq....Comments++++++++++++
         * *************  Début des données  *************************************************************
          c     x1            add       x2            x3                4  010203    cccc
          FFilename++IPEASF.....L.....A.Device+.Keywords+++++++++++++++++++++++++++++Comments++++++++++++
         * *************  Début des données  ************************************************************
          FCALCFM    CF   E             WORKSTN
          F                                     INFDS(DSECR)
          DName+++++++++++ETDsFrom+++To/L+++IDc.Keywords+++++++++++++++++++++++++++++Comments++++++++++++
         * *************  Début des données  ************************************************************
          D TSG             S              1    DIM(10)
          D DSECR           DS
          D  POSC                 370    371B 0
         */
        $this->dsline = array();
        $this->dsline['?'] = array(
            'carte' => array('pos' => 5, 'lng' => 1),
            'aster' => array('pos' => 6, 'lng' => 1),
            'line' => array('pos' => 7, 'lng' => 80),
            'free' => array('pos' => 6, 'lng' => 80)
        );
        $this->dsline['d'] = array(
            'carte' => array('pos' => 5, 'lng' => 1),
            'aster' => array('pos' => 6, 'lng' => 1),
            'line' => array('pos' => 7, 'lng' => 80),
            'varname' => array('pos' => 7, 'lng' => 15),
            'dstype' => array('pos' => 23, 'lng' => 2),
            'dsfrom' => array('pos' => 25, 'lng' => 7),
            'dsto' => array('pos' => 32, 'lng' => 7),
            'dsvartyp' => array('pos' => 39, 'lng' => 1),
            'dsvardec' => array('pos' => 40, 'lng' => 2),
            'keywords' => array('pos' => 43, 'lng' => 20)
        );
        $this->dsline['c'] = array(
            'carte' => array('pos' => 5, 'lng' => 1),
            'aster' => array('pos' => 6, 'lng' => 1),
            'line' => array('pos' => 7, 'lng' => 80),
            'factor1' => array('pos' => 11, 'lng' => 14),
            'opcode' => array('pos' => 25, 'lng' => 10),
            'factor2' => array('pos' => 35, 'lng' => 14),
            'result' => array('pos' => 49, 'lng' => 14),
            'length' => array('pos' => 63, 'lng' => 5),
            'decim' => array('pos' => 68, 'lng' => 2),
            'indHi' => array('pos' => 70, 'lng' => 2),
            'indLo' => array('pos' => 72, 'lng' => 2),
            'indEq' => array('pos' => 74, 'lng' => 2),
            'commt' => array('pos' => 76, 'lng' => 20)
        );
        $this->dsline['h'] = array(
            'carte' => array('pos' => 5, 'lng' => 1),
            'aster' => array('pos' => 6, 'lng' => 1),
            'line' => array('pos' => 7, 'lng' => 80)
        );
        $this->dsline['f'] = array(
            'carte' => array('pos' => 5, 'lng' => 1),
            'aster' => array('pos' => 6, 'lng' => 1),
            'line' => array('pos' => 7, 'lng' => 80)
        );
        
        parent::__construct($filename, $component_name, $type_component, $params);
    }

    public function conversion() {

        $this->filler1 = '     ';
        $filearray = explode(PHP_EOL, $this->fileloaded);
        if ($this->max_src_lines > 0 && count($filearray) > $this->max_src_lines) {
            $filearray = array_slice($filearray, 0, $this->max_src_lines );
            array_push($filearray, 
                    '*** ATTENTION : Fichier source tronqué à '.$this->max_src_lines . ' lignes ***' 
                    ) ;
        }

        $source = array();
        $carte_courante = 'h'; // première carte généralement rencontrée
        $ligne_precedente = '';
        $start_freecode = false;
        $last_opcode = '';
        $statut = 'fixed';

        foreach ($filearray as $srcline) {
            $leap_line = false;
            $structline = $this->splitds($srcline, $this->dsline['?']);

            if (!is_array($structline) || !array_key_exists('free', $structline)) continue;
            
            $temp_occur = strtolower(rtrim($structline['free']));
            if ($temp_occur == '/free') {
                $carte_courante = 'c';

                if (!$start_freecode) {
                    $start_freecode = true;
                    $source[$carte_courante] [] = $this->filler1 . ' /free';
                }
                $leap_line = true;
            } else {
                if ($temp_occur == '/end-free') {
                    $carte_courante = 'c';
                    $start_freecode = false;
                    $source[$carte_courante] [] = $this->filler1 . ' /end-free';
                    $leap_line = true;
                }
            }
            if (!$leap_line) {
                if (trim($structline['carte']) != '') {
                    $carte_courante = strtolower($structline['carte']);
                } else {
                    if ($start_freecode) {
                        if ($structline['aster'] == '*') {
                            if (trim($structline['line']) != '') {
                                $source [$carte_courante][] = $this->filler1 . 
                                        $this->filler1 . '//' . $structline['line'];
                            } else {
                                $source [$carte_courante][] = '';
                            }
                        } else {
                            $source ['c'][] = $srcline;
                        }
                        $leap_line = true;
                    }
                }
            }
            if (!$leap_line) {
                if (isset($structline['carte'])) {
                    if ($structline['aster'] == '*') {
                        if (!$start_freecode) {
                            $source [$carte_courante][] = $srcline;
                        } else {
                            if (trim($structline['line']) != '') {
                                $source [$carte_courante][] = $this->filler1 . 
                                        $this->filler1 . '// ' . 
                                        trim($structline['line']);
                            }
                        }
                        $leap_line = true;
                    }
                } else {
                    if (!$start_freecode) {
                        $source [$carte_courante][] = $srcline;
                    } else {
                        $source [$carte_courante][] = $this->filler1 . 
                                $this->filler1 . '// ' . trim($srcline);
                    }
                    $leap_line = true;
                }
            }
            if (!$leap_line) {
                $structline['carte'] = trim(strtolower($structline['carte']));
                if ($structline['carte'] == '') {
                    $structline['carte'] = $carte_courante;
                }
                switch ($structline['carte']) {
                    case 'h': {
                            if ($carte_courante == '') {
                                $carte_courante = 'h';
                            }
                            $last_opcode = '';
                            $source['h'] [] = $srcline;
                            break;
                        }
                    case 'f' : {
                            if ($carte_courante == '') {
                                $carte_courante = 'f';
                            }
                            $last_opcode = '';
                            $source['f'] [] = $srcline;
                            break;
                        }
                    case 'd' : {                       
                            if ($carte_courante == '') {
                                $carte_courante = 'd';
                            }
                            $last_opcode = '';
                            $source['d'] [] = $srcline;
                            $structline = $this->splitds(
                                            $srcline, 
                                            $this->dsline['d']
                                            );
                            $result = $this->analyseCarteD($structline);
                            break;
                        }
                    case 'c' : {
                            if ($carte_courante == '') {
                                $carte_courante = 'c';
                            }
                            $structline = $this->splitds(
                                            $srcline, 
                                            $this->dsline['c']
                                            );
                            // Traitement du code SQL (conservé tel quel)
                            // TODO : conversion du SQL en free à implémenter ultérieurement
                            if ($structline['aster'] == '/' || 
                                    $structline['aster'] == '+') {
                                $last_opcode = '';
                                if (!$start_freecode) {
                                    $start_freecode = true;
                                    $source[$carte_courante] [] = 
                                            $this->filler1 . ' /free';
                                }
                                $tag_sql = strtoupper(trim($structline['line']));
                                if ($tag_sql == 'EXEC SQL') {
                                    $nb_filler = 2;
                                } else {
                                    if ($tag_sql == 'END-EXEC') {
                                        $structline['line'] = ';';
                                    }
                                    $nb_filler = 3;
                                }
                                $source[$carte_courante] [] = 
                                        str_repeat($this->filler1, $nb_filler) . 
                                        $structline['line'];
                            } else {
                                if ($structline['aster'] == '*' || 
                                        trim($structline['opcode']) != '') {
                                    // on efface le dernier opcode dans ce cas
                                    $last_opcode = '';
                                }
                                if ($last_opcode == 'EVAL') {
                                    $ligne_precedente = 'EVAL';
                                }
                                list($rpgcode, $statut, $ligne_precedente) = 
                                        $this->analyseCarteC(
                                                $structline, 
                                                $ligne_precedente
                                                );

                                if (trim($rpgcode) != '') {
                                    if (!$start_freecode) {
                                        if ($statut == 'free') {
                                            $start_freecode = true;
                                            $source[$carte_courante] [] = 
                                                    $this->filler1 . ' /free';
                                        }
                                    } else {
                                        if ($statut == 'fixed' && $start_freecode) {
                                            $start_freecode = false;
                                            $source[$carte_courante] [] = 
                                                    $this->filler1 . ' /end-free';
                                        }
                                    }
                                    if ($statut == 'free' || 
                                            $statut == 'complement') {
                                        if ($statut == 'free') {
                                            if (trim($structline['opcode']) == '' && 
                                                    $ligne_precedente == 'EVAL') {
                                                // on supprime le point virgule de la ligne précédente et on conserve 
                                                // celui de la ligne courante (au moins temporairement)
                                                $tmp_count = 
                                                        count($source[$carte_courante]) - 1;
                                                $source[$carte_courante][$tmp_count] = 
                                                        str_replace(
                                                            '{pointvirgule}', 
                                                            '', 
                                                            $source[$carte_courante][$tmp_count]
                                                        );
                                            }
                                            $source[$carte_courante] [] = $this->filler1 . 
                                                    $this->filler1 . trim($rpgcode);
                                        } else {
                                            $tmp_count = count($source[$carte_courante]) - 1;
                                            $source[$carte_courante][$tmp_count] = 
                                                    str_replace(
                                                        '{compl}', 
                                                        $rpgcode . '{compl}', 
                                                        $source[$carte_courante][$tmp_count]
                                                    );
                                        }
                                    } else {
                                        $source[$carte_courante] [] = $srcline;
                                    }
                                }
                            }
                            if (isset($structline['opcode']) && trim($structline['opcode']) != '') {
                                $last_opcode = trim(strtoupper($structline['opcode']));
                            }
                            break;
                        }
                    default: {
                            $last_opcode = '';
                            $source['x'] [] = $srcline;
                        }
                }
            }
        }
        if ($start_freecode || $statut == 'free') {
            $source['c'] [] = $this->filler1 . ' /end-free';
        }

        if (count($this->variables) > 0) {
            $format = '     D %1$-15s S             %2$2s %3$2s';
            foreach ($this->variables as $var_key => $var_value) {
                $source['d'] [] = sprintf(
                            $format, 
                            $var_key, 
                            $var_value['length'], 
                            $var_value['decim']
                        );
            }
        }

        $this->source = '';
        if (isset($source['h']) && !empty($source['h'])) {
            $this->source .= implode(PHP_EOL, $source['h']);
        }
        if (isset($source['f']) && !empty($source['f'])) {
            $this->source .= PHP_EOL . implode(PHP_EOL, $source['f']);
        }
        if (isset($source['d']) && !empty($source['d'])) {
            $this->source .= PHP_EOL . implode(PHP_EOL, $source['d']);
        }
        if (isset($source['c']) && !empty($source['c'])) {
            $this->source .= PHP_EOL . implode(PHP_EOL, $source['c']);
        }
        if (isset($source['x']) && !empty($source['x'])) {
            $this->source .= PHP_EOL . implode(PHP_EOL, $source['x']);
        }
        // nettoyage des tags "utilitaires" placés dans le code durant la conversion
        $this->source = str_replace('{compl}', '', $this->source);
        $this->source = str_replace('{pointvirgule}', ';', $this->source);
    }

    protected function analyseCarteC($srcline, $ligne_precedente = '') {
        $reponse = '';
        $statut = 'fixed';
        $srcline['condit'] = '';
        $ligne_suivante = '';
        $srcline['opcode'] = strtoupper($srcline['opcode']);
        if (substr($srcline['opcode'], 0, 2) == 'IF') {
            $srcline['condit'] = substr($srcline['opcode'], 2, 2);
            $srcline['opcode'] = substr($srcline['opcode'], 0, 2);
        } else {
            if (substr($srcline['opcode'], 0, 3) == 'DOW') {
                $srcline['condit'] = substr($srcline['opcode'], 3, 2);
                $srcline['opcode'] = substr($srcline['opcode'], 0, 3);
            } else {
                if (substr($srcline['opcode'], 0, 3) == 'AND') {
                    $srcline['condit'] = substr($srcline['opcode'], 3, 2);
                    $srcline['opcode'] = substr($srcline['opcode'], 0, 3);
                } else {
                    if (substr($srcline['opcode'], 0, 2) == 'OR') {
                        $srcline['condit'] = substr($srcline['opcode'], 2, 2);
                        $srcline['opcode'] = substr($srcline['opcode'], 0, 2);
                    }
                }
            }
        }
        $srcline['opcode'] = trim($srcline['opcode']);
        $srcline['condit'] = trim($srcline['condit']);
        $srcline['result'] = trim($srcline['result']);
        $srcline['factor1'] = trim($srcline['factor1']);
        $srcline['factor2'] = trim($srcline['factor2']);
        $srcline['length'] = trim($srcline['length']);
        $srcline['decim'] = trim($srcline['decim']);
        if ($srcline['length'] != '' && $ligne_precedente != 'EVAL') {
            if (trim($srcline['decim']) != '') {
                $vartype = 'n';  // type numérique
            } else {
                $vartype = 'a';  // type alpha
            }
            $this->variables [$srcline['result']] = array(
                'length' => intval($srcline['length']), 
                'decim' => $srcline['decim'], 
                'type' => $vartype);
            $this->variable_tabs [trim($srcline['result'])] = 
                    $this->variables [$srcline['result']];
        }
     
        switch ($srcline['opcode']) {
            case 'PLIST' :
            case 'PARM' : {
                    $reponse = 'xxx';
                    $statut = 'fixed';
                    break;
                }
            case 'EVAL' : {
                    $reponse = str_ireplace('eval ', '', $srcline['line']);
                    $reponse = trim($reponse) . PHP_EOL;
                    $statut = 'free';
                    $ligne_suivante = 'EVAL';
                    break;
                }
            case 'READC' : {
                    //TODO : conversion des ordres de lecture en free à implémenter ultérieurement
                    $reponse = 'xxx';
                    $statut = 'fixed';
                    break;
                }
            case 'CLEAR' :
            case 'READP' :
            case 'READ':
            case 'READE' :
            case 'READPE' :
            case 'CHAIN' : {
                    $reponse = $srcline['opcode'] . ' ' . $srcline['factor2'] . ' ;';
                    if (isset($srcline['indEq']) && trim($srcline['indEq']) != '') {
                        if ($srcline['opcode'] == 'CHAIN') {
                            $reponse .= PHP_EOL . $this->filler1 . 
                                    $this->filler1 . '*IN' . $srcline['indEq'] . 
                                    ' = %found(' . $srcline['factor2'] . ') ;';
                        } else {
                            $reponse .= PHP_EOL . $this->filler1 . $this->filler1 . 
                                    '*IN' . $srcline['indEq'] . ' = %EOF ;';
                        }
                    }
                    $statut = 'free';
                    break;
                }
            case 'SETGT' :
            case 'SETLL' : {
                    $reponse = $srcline['opcode'] . ' ' . $srcline['factor1'] . 
                            ' ' . $srcline['factor2'] . ' ;';
                    $statut = 'free';
                    break;
                }
            case 'MOVE' :
            case 'MOVEL' : {
                    $reponse = 'xxx';
                    $statut = 'fixed';  // statut par défaut
                    /*
                     * Autre cas de figure possible : le facteur d'origine est
                     * une chaîne de caractères, et la variable de destination
                     * est de type alpha. 
                     */
                    $first_car = substr($srcline['factor2'], 0, 1);
                    $is_string = ($first_car == "'" || $first_car == '*') ?
                            true : false ;
                    if ($is_string) {
                        //if (isset($this->variable_tabs[$srcline_result]) &&
                        //        $this->variable_tabs[$srcline_result]['type'] == 'a' ) {
                            $reponse = $srcline['result'] . ' = ' . $srcline['factor2'] . ' ;';
                            $statut = 'free';
                            break;
                        //}
                    }
                    /*
                     * Les MOVE et MOVEL ne sont convertis en Free uniquement
                     * dans le cas où les 2 variables sont clairement identifiées 
                     * et sont de même type alpha.  
                     */
                    $srcline_result = trim($srcline['result']);
                    $srcline_factor2 = trim($srcline['factor2']);

                    if (isset($this->variable_tabs[$srcline_result]) &&
                            isset($this->variable_tabs[$srcline_factor2])) {
                        if ($this->variable_tabs[$srcline_result]['type'] ==
                                $this->variable_tabs[$srcline_factor2]['type'] &&
                                $this->variable_tabs[$srcline_result]['type'] == 'a') {
                            $reponse = $srcline_result . ' = ' . $srcline_factor2 . ' ;';
                            $statut = 'free';
                            break;
                        }
                    }

                    break;
					
                }
            case 'Z-ADD' : {
                    $reponse = $srcline['result'] . ' = ' . $srcline['factor2'] . ' ;';
                    $statut = 'free';
                    break;
                }
            case 'ADD' :
            case 'MULT' :
            case 'SUB' : {
                    $oper = array_key_exists($srcline['opcode'], $this->opermaths) ? 
                            $this->opermaths[$srcline['opcode']] : 
                            '?' . $srcline['opcode'] . '?';
                    if (trim($srcline['factor1']) != '') {
                        $reponse = $srcline['result'] . ' = ' . $srcline['factor1'] . 
                                ' ' . $oper . ' ' . $srcline['factor2'] . ' ;';
                    } else {
                        $reponse = $srcline['result'] . ' ' . $oper . '= ' . 
                                $srcline['factor2'] . ' ;';
                    }
                    $statut = 'free';
                    break;
                }
            case 'DIV' : {
                    $reponse = $srcline['result'] . ' = ' . 
                            "%DIV({$srcline['factor1']}:{$srcline['factor2']});";
                    // pour le cas où sur la ligne suivante on demanderait à récupérer le reste de la division
                    $ligne_suivante = "%REM({$srcline['factor1']}:{$srcline['factor2']});";
                    $statut = 'free';
                    break;
                }
            case 'MVR' : {
                    $reponse = $srcline['result'] . ' = ' . $ligne_precedente;
                    $statut = 'free';
                    break;
                }
            case 'IF' : {
                    if ($srcline['condit'] == '') {
                        $reponse = 'If ( ' . $srcline['factor2'] . ' ) {compl};';
                    } else {
                        $condit = array_key_exists($srcline['condit'], $this->conditions) ? 
                                $this->conditions[$srcline['condit']] : 
                                '?' . $srcline['condit'] . '?';
                        $reponse = 'If ( ' . $srcline['factor1'] . ' ' . $condit .
                                ' ' . $srcline['factor2'] . ' ) {compl};';
                    }
                    $statut = 'free';
                    break;
                }
            case 'DOW' : {
                    if (trim($srcline['condit']) == '') {
                        $reponse = 'Dow ( ' . $srcline['factor2'] . ' ) {compl};';
                    } else {
                        $condit = array_key_exists($srcline['condit'], $this->conditions) ? 
                                $this->conditions[$srcline['condit']] : 
                                '?' . $srcline['condit'] . '?';
                        $reponse = 'Dow ( ' . $srcline['factor1'] . ' ' . 
                                $condit . ' ' . $srcline['factor2'] . ' ) {compl};';
                    }
                    $statut = 'free';
                    break;
                }
            case 'OR' :
            case 'AND' : {
                    $condjoint = array_key_exists($srcline['opcode'], $this->condjoint) ? 
                            $this->condjoint[$srcline['opcode']] : 
                            '?' . $srcline['opcode'] . '?';
                    $condit = array_key_exists($srcline['condit'], $this->conditions) ? 
                            $this->conditions[$srcline['condit']] : 
                            '?' . $srcline['condit'] . '?';
                    $reponse = $condjoint . ' ( ' . $srcline['factor1'] . ' ' . 
                            $condit . ' ' . $srcline['factor2'] . ' ) ';
                    $statut = 'complement'; // complément à intégrer sur la condition de la ligne précédente
                    break;
                }
            case 'ENDIF' : {
                    $reponse = 'Endif ;';
                    $statut = 'free';
                    break;
                }
            case 'ENDDO' : {
                    $reponse = 'EndDo ;';
                    $statut = 'free';
                    break;
                }
            case 'ELSE' : {
                    $reponse = 'Else ;';
                    $statut = 'free';
                    break;
                }
            case 'BEGSR' : {
                    $reponse = 'Begsr ' . $srcline['factor1'] . ' ;';
                    $statut = 'free';
                    break;
                }
            case 'EXFMT' : {
                    $reponse = 'Exfmt ' . $srcline['factor2'] . ' ;';
                    $statut = 'free';
                    break;
                }
            case 'WRITE' : {
                    $reponse = 'Write ' . $srcline['factor2'] . ' ;';
                    $statut = 'free';
                    break;
                }
            case 'EXSR' : {
                    $reponse = 'Exsr ' . $srcline['factor2'] . ' ;';
                    $statut = 'free';
                    break;
                }
            case 'ENDSR' : {
                    $reponse = 'Exsr ' . trim($srcline['factor2']) . ';';
                    $statut = 'free';
                    break;
                }
            case 'DUMP' : {
                    $reponse = 'Dump ' . trim($srcline['factor2']) . ';';
                    $statut = 'free';
                    break;
                }
            case 'SETOFF' :
            case 'SETON' : {
                    $reponse = '*IN' . $srcline['indHi'] . ' = ';
                    if ($srcline['opcode'] == 'SETOFF') {
                        $reponse .= '*OFF ;';
                    } else {
                        $reponse .= '*ON ;';
                    }
                    $statut = 'free';
                    break;
                }
            default : {
                    if ($ligne_precedente == 'EVAL') {
                        $reponse = ' ' . trim($srcline['line']) . '{pointvirgule}';
                        $statut = 'free';
                        $ligne_suivante = $ligne_precedente;
                    } else {
                        $reponse = 'xxx';
                        $statut = 'fixed';
                    }
                }
        }
        return array($reponse, $statut, $ligne_suivante);
    }

    protected function analyseCarteD($srcline) {
        if (trim($srcline['varname']) != '') {
            if ($srcline['dsvardec'] != '') {
                $vartype = 'n';
            } else {
                $vartype = 'a';
            }
            if (substr($srcline['keywords'], 0, 3) == 'DIM') {
                $vartype .= 't';
            }
            if ($srcline['dstype'] == 'DS') {
                $longueur = intval($srcline['dsfrom']) - intval($srcline['dsto']) ;
            } else {
                $longueur = intval($srcline['dsto']) ;
            }
            $this->variable_tabs [trim($srcline['varname'])] = array(
                'length' => $longueur, 
                'decim' => $srcline['dsvardec'], 
                'type' => $vartype);
        }
        return true;
    }

    protected function analyseCarteF($srcline) {
        return true;
    }

}

