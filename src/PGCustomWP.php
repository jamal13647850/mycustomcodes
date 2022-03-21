<?php

namespace jamal\mycustomcodes;

class PGCustomWP{

    
    private string $footerContent;

    public function __construct() {
        
        // add_filter( 'update_footer', array($this,'change_footer_version'), 9999);
        // add_filter( 'the_excerpt_rss', 'rss_thumbnail' );
        // add_filter( 'the_content_feed', 'rss_thumbnail' );
        // add_action('admin_menu', array($this,'in_remove_menu_elements'), 999);
        // add_action('wp_dashboard_setup', array($this,'remove_dashboard_widgets') );
        // add_action( 'wp_before_admin_bar_render', array($this,'remove_admin_bar_links') );
        // add_action('admin_head', array($this,'hide_help'));
        // add_action('login_head', array($this,'my_custom_login_logo'));
        // add_action('admin_head', array($this,'custom_logo'));

        // add_filter('login_headertitle', array($this,'change_wp_login_title'),9999);
        // add_filter('wp_mail_from', array($this,'new_mail_from'));
        // add_filter('wp_mail_from_name', array($this,'new_mail_from_name'));
        // remove_action('wp_head', 'wp_generator');
        // remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
        // add_filter('show_admin_bar', '__return_false');
        // add_action( 'phpmailer_init', 'add_email_signature_hook_phpmailer_init' );
        // add_filter('the_generator', 'remove_version');
        // add_filter( 'manage_media_columns', array($this,'muc_column') );
        // add_action( 'manage_media_custom_column', array($this,'muc_value'), 10, 2 );
        // add_filter( 'style_loader_src', array($this,'port_remove_cssjs_ver'), 10, 2 );
        // add_filter( 'script_loader_src', array($this,'port_remove_cssjs_ver'), 10, 2 );
    }

    public function setFooterContent(string $footerContent):PGCustomWP{
        $this->footerContent=$footerContent;
        return $this;
    }
    public function changeFooterContentHookInto(){
        $CI = $this;
        add_filter('admin_footer_text', function() use ($CI){
            echo ($CI->footerContent);
        },9999,1);
    }
    public function changeFooterContent(){
        echo ($this->footerContent);
    }

    public function createTable(string $createQuery){
        dbDelta($createQuery);
    }
}