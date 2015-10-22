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

    /**
     * get terms of custom taxonomy
     * @param $postid
     * @param $custom_taxonomy
     */
    function get_cpt_taxonomy($postid,$custom_taxonomy){
        $terms = get_the_terms( $postid, $custom_taxonomy );
        if ($terms){
            foreach($terms as $term) {
                echo '<a href="' . get_category_link( $term->term_id ) . '" title="' . sprintf( __( "View all posts in %s","vai" ), $term->name ) . '" ' . '>' . $term->name.'</a> , ';
            }
        }
    }

} 