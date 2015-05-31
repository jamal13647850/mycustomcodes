<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
class PGTaxonomies {
    private $vars = array();
    public function __construct($param) {


    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function __call($name, $arguments) {

    }

    public function RegiterTaxonomies(){
        register_taxonomy( $this->vars['taxonomy'], $this->vars['object_type'], $this->vars['args']);
    }
    function active(){
        add_action('init',array($this, 'RegiterTaxonomies'));
    }

} 