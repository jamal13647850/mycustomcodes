<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
namespace pgsavis;
class PGPostType {
    private $vars = array();
    public function __construct($param) {
        $this->vars['page_title']='page_title';
        $this->vars['menu_title']='menu_title';
        $this->vars['capability']='edit_posts';
        $this->vars['menu_slug']='menu_slug';
        $this->vars['output']=__('Created By Sayyed Jamal Ghasemi www.medicalinfo.ir');
        $this->vars['posts_per_page']=-1;
    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function __call($name, $arguments) {

    }

    /**
     * @param $args for register post types
     * this function register new post type
     */
    Public function RegisterPostTypes(){
        register_post_type( $this->vars['posttype'],$this->vars['args'] );
        //flush_rewrite_rules();
    }

    /**
     * active post type
     */
    function active(){
        add_action('init',array($this, 'RegisterPostTypes'));
        add_action('admin_menu' , array($this, 'brdesign_enable_pages'));

        if ( !is_admin() ) {
            //add_filter( 'pre_get_posts', array($this,'cpt_posts_per_page') );
        }
        if ( is_admin() ) {
            add_filter('post_row_actions', array($this,'pg_post_or_page_row_actions'), 10, 2);
            add_filter('page_row_actions', array($this,'pg_post_or_page_row_actions'), 10, 2);
        }

    }

    /**
     * this functions add new page to posttype
     */
    function brdesign_enable_pages() {
        add_submenu_page('edit.php?post_type=pgquestionanswer', $this->vars['page_title'],$this->vars['menu_title'], $this->vars['capability'], $this->vars['menu_slug'], array($this,'custom_function'));
    }
    function custom_function(){
        //this is from PGOptions Class
        global $pgqaop;
        $pgqaop->create_options_form();
    }

    /**
     * @param $actions
     * @param $page_object
     * @return array
     * add new post or page actions link
     * need set a function name to $this->vars['call']
     */
    function pg_post_or_page_row_actions($actions, $page_object)
    {
        if  (isset($this->vars['call'])){
            $act= call_user_func( $this->vars['call'],'data');
            if (is_array($act)){
                $actions=array_merge($actions,$act);
            }
        }


        return $actions;
    }
    function cpt_posts_per_page( $query ) {
        if ( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == $this->vars['posttype'] ) $query->query_vars['posts_per_page'] =$this->vars['posts_per_page'];
        return $query;
    }


} 