<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
class PGPostType {
    private $vars = array();
    public function __construct($param) {
        $this->vars['page_title']='page_title';
        $this->vars['menu_title']='menu_title';
        $this->vars['capability']='edit_posts';
        $this->vars['menu_slug']='menu_slug';
        $this->vars['output']=__('Created By Sayyed Jamal Ghasemi www.pgsavis.com');
        $this->vars['posts_per_page']=12;
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
            add_filter( 'pre_get_posts', array($this,'cpt_posts_per_page') );
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
     * @example php:
     * $pgqapt->call='add_send_mail_actions';
        function add_send_mail_actions(){
        global $post;
        if ($post->post_type == "pgquestionanswer"){
        $author_id=$post->post_author;
        $mail=get_the_author_meta( 'user_email',  $author_id );
        $permalink = get_permalink();
        $actions['sendmail'] = '<a href="#" class="sendmail" '.'data-author='.$mail.' data-link='. $permalink.'>' . __('send mail','vai') . '</a>';
        }
        return $actions;
        }
     * @example js:
     * jQuery( document ).on( "click", "a.sendmail", function() {
        var el=jQuery(this);
        var authormail =jQuery(this).data("author");
        var questionlink =jQuery(this).data("link");
        jQuery.ajax({

        url:pgqaajax.ajaxurl,
        type:'post',
        data:{
        action:'send_mail_link_n',
        authormail:authormail,
        questionlink:questionlink
        },
        success:function(resp){
        alert(resp);
        }
        });
        return false;
        });
     *
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