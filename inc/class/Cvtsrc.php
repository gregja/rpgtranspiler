<?php

/**
 * Description of Cvtsrc
 *
 * @author gjarrige
 */
interface interfaceCvtsrc {

    public function __construct($filename, $component_name, $type_component, $params = array());

    public function loadFile($filename);

    public function conversion();

    public function retrieve();

    public function display();
    
    public function splitds ($string2analyse, $datastructure=array());
}

class Cvtsrc implements interfaceCvtsrc {

    public $fileloaded;
    public $filearray;
    public $filename;
    public $source;
    public $variables;
    public $variable_tabs;
    public $component_name;
    public $type_component;
    public $params_ins;
    public $params_out;
    protected $conditions = array() ;
    protected $opermaths = array() ;
    protected $condjoint = array() ;
    protected $filler1 = '' ;
    protected $version_code = '' ;
    protected $version_date = '' ;
    protected $max_src_lines = '' ;
    protected $dsline = array();

    public function __construct($filename, $component_name, $type_component, $params = array()) {

        $this->source = '';
        $this->variables = array();
        $this->variable_tabs = array();
        $this->filename = $filename;
        $this->loadFile($filename);
        $this->component_name = $component_name;
        $this->type_component = $type_component;
        $this->params_ins = is_array($params) ? $params : array();
        $this->params_out = array();
    }

    public function loadFile($filename) {
        if (is_readable($filename)) {
            $this->fileloaded = file_get_contents($filename);
        }
    }

    /*
     * méthode à "overrider" dans la classe fille 
     */
    public function conversion() {
        
    }

    public function retrieve() {
        return $this->source;
    }

    public function display() {
        return nl2br($this->source);
    }

    public function splitds ($string2analyse, $datastructure=array()) {
        if (!is_array($datastructure) || count($datastructure)<= 0){
            return false;
        }
        if (strlen ($string2analyse) <= 0 ) {
            return false;
        }
        $structure = array() ;
        foreach ($datastructure as $key=>$data) {
            if(isset($data['pos']) && isset($data['lng'])) {
                $structure[$key] = substr($string2analyse, $data['pos'], $data['lng']) ;
            }
        }
        return $structure;
    }
}

