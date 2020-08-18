<?php
/**
  *@author Sayyed Jamal Ghasemi <https://www.linkedin.com/in/jamal1364/>
  *@version 1.0.0
 * 
 */
namespace jamal13647850\mycustomcodes;
class CustomWP{
    private $vars = array() ;
    function __construct($param) {
        isset($param['footercontent'])?$this->vars['footercontent']=$param['footercontent']:$this->vars['footercontent']='<a class="text-center" href="http://www.jamal13647850.com">طراحی و اجرا پدیده گستر ساویس</a>';
        isset($param['footerversion'])?$this->vars['footerversion']=$param['footerversion']:$this->vars['footerversion']='';
        isset($param['deactiveplugin'])?$this->vars['deactiveplugin']=$param['deactiveplugin']:$this->vars['deactiveplugin']=array();
        isset($param['logo'])?$this->vars['logo']=$param['logo']:$this->vars['logo']='';
        isset($param['mailfrom'])?$this->vars['mailfrom']=$param['mailfrom']:$this->vars['mailfrom']=get_bloginfo('admin_email');
        isset($param['mailname'])?$this->vars['mailname']=$param['mailname']:$this->vars['mailname']=get_bloginfo();
        isset($param['mailSignature'])?$this->vars['mailSignature']=$param['mailSignature']:$this->vars['mailSignature']=get_bloginfo();
        
    }
    function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    function __get($name) {
        return $this->vars[$name];
    }
    function __call($name, $arguments) {
        
    }
    /**************************************************************************/
    function change_footer_content() {
	echo ($this->vars['footercontent']);
    }
    function change_footer_version() {
	return ($this->vars['footerversion']);
    }
    function rss_thumbnail ( $content ) {
        global $post;
        if ( isset( $post  ) ) {
            if ( has_post_thumbnail( $post->ID ) )
                $content = get_the_post_thumbnail( $post->ID, 'thumbnail', array( 'alt' => get_the_title(), 'title' => get_the_title(), 'style' => 'float:right;' ) ) . '' . $content;
            }
        return $content;
    }
    function remove_menus() {
	global $menu;
	$restricted = array(__('Dashboard'), __('Posts'), __('Media'), __('Links'), __('Pages'), __('Appearance'), __('Tools'), __('Users'), __('Settings'), __('Comments'), __('Plugins'));
	end ($menu);
	while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
	}
    }
    function in_remove_menu_elements(){
        global $submenu,$menu;
        remove_submenu_page( 'themes.php', 'theme-editor.php' );
        remove_submenu_page( 'themes.php', 'themes.php' );
        unset($submenu['themes.php'][6]); // remove customize link
        unset($submenu['themes.php'][15]); // remove customize header link
        unset($menu[75]); // remove tools link
        remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
    }
    /**
    * Remove edit link for all plugins and Remove deactivate link for important plugins
    */
    function disable_plugin_deactivation( $actions, $plugin_file, $plugin_data, $context ) {
	// Remove edit link for all plugins
	if ( array_key_exists( 'edit', $actions ) )
		unset( $actions['edit'] );
	// Remove deactivate link for important plugins
	if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, $this->vars['deactiveplugin']))
            unset( $actions['deactivate'] );
            return $actions;
        }
    /**
     * 
     */
    function remove_dashboard_widgets() {
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
	// bbpress
	unset($wp_meta_boxes['dashboard']['normal']['core']['bbp-dashboard-right-now']);
	// yoast seo
	unset($wp_meta_boxes['dashboard']['normal']['core']['yoast_db_widget']);
	// gravity forms
	unset($wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['woocommerce_persian_feed']);
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
        /*remove woocomrce widget*/
        unregister_widget( 'WC_Widget_Recent_Products' );
        unregister_widget( 'WC_Widget_Featured_Products' );
        unregister_widget( 'WC_Widget_Product_Categories' );
        unregister_widget( 'WC_Widget_Product_Tag_Cloud' );
        unregister_widget( 'WC_Widget_Cart' );
        unregister_widget( 'WC_Widget_Layered_Nav' );
        unregister_widget( 'WC_Widget_Layered_Nav_Filters' );
        unregister_widget( 'WC_Widget_Price_Filter' );
        unregister_widget( 'WC_Widget_Product_Search' );
        unregister_widget( 'WC_Widget_Top_Rated_Products' );
        unregister_widget( 'WC_Widget_Recent_Reviews' );
        unregister_widget( 'WC_Widget_Recently_Viewed' );
        unregister_widget( 'WC_Widget_Best_Sellers' );
        unregister_widget( 'WC_Widget_Onsale' );
        unregister_widget( 'WC_Widget_Random_Products' ); 
    }
    function pg_dashboard_widget_function() {
        echo ($this->vars['newdashbordwidget']['content']);
    }
    function pg_add_dashboard_widgets() {
        wp_add_dashboard_widget($this->vars['newdashbordwidget']['id'], $this->vars['newdashbordwidget']['name'], array($this,'pg_dashboard_widget_function'));
    }
    function no_update_notification() {
	remove_action('admin_notices', 'update_nag', 3);
    }
    function change_admin_color($result) {
	$colors=array('default','ectoplasm','light','blue','coffee','midnight','ocean','sunrise');
	return $colors[$this->vars['admincolor']];
    }
    function remove_admin_bar_links() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
        $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
        $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
        $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
        $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
        $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
        //$wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
        $wp_admin_bar->remove_menu('view-site');        // Remove the view site link
        $wp_admin_bar->remove_menu('updates');          // Remove the updates link
        $wp_admin_bar->remove_menu('comments');         // Remove the comments link
        $wp_admin_bar->remove_menu('new-content');      // Remove the content link
        $wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
        //$wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
    }
    function hide_help() {
	echo '<style type="text/css">
                #contextual-help-link-wrap { display: none !important; }
	     </style>';
    }
    function my_custom_login_logo() {
        echo '<style type="text/css">
                h1 a { 
			background-image:url('.$this->vars['logo'].') !important; 
		}
		.login h1 a{
			background-size: 300px auto !important;
			width: 250px !important;
			height:176px !important;
		}
		/*
		#loginform {
   			background-color:#B0B2B6;
		}
		.login label {
  			color:#FFF;
		}
		
		.login .button-primary {
    		background-image:none !important;
    		background-color:#8f919d !important;
    		border:1px solid #666 !important;
    		-webkit-box-shadow: inset 0 1px 0 rgba(230,230,230,0.5) !important;
    		box-shadow: inset 0 1px 0 rgba(230,230,230,0.5) !important;
		}
		.login .button-primary:hover {
   			 background-color:#757580 !important;
		}
		*/
        </style>';
    }
    function custom_logo() {
        echo '<style type="text/css">
                #header-logo { background-image: url('.$this->vars['logo'].') !important; }
            </style>';
    }

    function change_wp_login_title() {
	return get_bloginfo();
    }
    function new_mail_from($old) {
        return ($this->vars['mailfrom']);
    }
    function new_mail_from_name($old) {
        return ($this->vars['mailname']);
    }
    function change_post_menu_text() {
        global $menu;
        global $submenu;
        // Change menu item
        $menu[5][0] = $this->vars['newmenutext']['main'];
        // Change post submenu
        $submenu['edit.php'][5][0] = $this->vars['newmenutext']['sub1'];
        $submenu['edit.php'][10][0] = $this->vars['newmenutext']['sub2'];
    }
    function change_post_type_labels() {
        global $wp_post_types;
        // Get the post labels
        $postLabels = $wp_post_types['post']->labels;
        $postLabels->name = $this->vars['newlabel']['name'];
        $postLabels->singular_name = $this->vars['newlabel']['name'];
        $postLabels->add_new = $this->vars['newlabel']['add_new'];
        $postLabels->add_new_item = $this->vars['newlabel']['add_new'];
        $postLabels->edit_item = $this->vars['newlabel']['edit_item'];
        $postLabels->new_item = $this->vars['newlabel']['name'];
        $postLabels->view_item = $this->vars['newlabel']['view_item'];
        $postLabels->search_items = $this->vars['newlabel']['search_items'];
        $postLabels->not_found = $this->vars['newlabel']['not_found'];
        $postLabels->not_found_in_trash = $this->vars['newlabel']['not_found_in_trash'];
    }
    function new_admin_bar_link() {
        global $wp_admin_bar;
        $wp_admin_bar->add_menu( array(
            'id' => $this->vars['newadminbarlink']['id'],
            'title' =>  $this->vars['newadminbarlink']['title'],
            'href' => $this->vars['newadminbarlink']['href']
            ) 
        );
    }
    function add_email_signature_hook_phpmailer_init($mobj) {
	if (!is_object($mobj) || !is_a($mobj, 'PHPMailer')) return;
        $sig = $this->vars['mailSignature'];
        $body = ($mobj->ContentType == "text/plain") ? $mobj->Body : $mobj->AltBody;
        if (!preg_match("/^-- /",$body)) $body .= "\n-- \n".$sig;
	if ($mobj->ContentType == "text/plain") {
		$mobj->Body = $body;
	} else {
		$mobj->AltBody = $body;
	}
    }
    function remove_version() {
        return '';
    }
    function muc_column( $cols ) {
        $cols["media_url"] = "URL";
        return $cols;
    }
    function muc_value( $column_name, $id ) {
        if ( $column_name == "media_url" ) echo '<input type="text" width="100%" onclick="jQuery(this).select();" value="'. wp_get_attachment_url( $id ). '" readonly="true" />';
    }
    function port_remove_cssjs_ver( $src ) {
        if( strpos( $src, '?ver=' ) )
            $src = remove_query_arg( 'ver', $src );
        return $src;
    }
    /**************************************************************************/
    function Runfilters(){
        add_filter('admin_footer_text', array($this,'change_footer_content'),9999,1);
        add_filter( 'update_footer', array($this,'change_footer_version'), 9999);
        add_filter( 'the_excerpt_rss', 'rss_thumbnail' );
        add_filter( 'the_content_feed', 'rss_thumbnail' );
        add_action('admin_menu', array($this,'in_remove_menu_elements'), 999);
        add_action('wp_dashboard_setup', array($this,'remove_dashboard_widgets') );
        add_action( 'wp_before_admin_bar_render', array($this,'remove_admin_bar_links') );
        add_action('admin_head', array($this,'hide_help'));
        add_action('login_head', array($this,'my_custom_login_logo'));
        add_action('admin_head', array($this,'custom_logo'));

        add_filter('login_headertitle', array($this,'change_wp_login_title'),9999);
        add_filter('wp_mail_from', array($this,'new_mail_from'));
        add_filter('wp_mail_from_name', array($this,'new_mail_from_name'));
        remove_action('wp_head', 'wp_generator');
        remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
        add_filter('show_admin_bar', '__return_false');
        add_action( 'phpmailer_init', 'add_email_signature_hook_phpmailer_init' );
        add_filter('the_generator', 'remove_version');
        add_filter( 'manage_media_columns', array($this,'muc_column') );
        add_action( 'manage_media_custom_column', array($this,'muc_value'), 10, 2 );
        add_filter( 'style_loader_src', array($this,'port_remove_cssjs_ver'), 10, 2 );
        add_filter( 'script_loader_src', array($this,'port_remove_cssjs_ver'), 10, 2 );
    }
     function RemoveAllMenus(){
        add_action('admin_menu', array($this,'remove_menus'));
    }   
    function DebugMenu(){
        if (!function_exists('debug_admin_menus')):
            function debug_admin_menus() {
                global $submenu, $menu, $pagenow;
                if ( current_user_can('manage_options') ) { // ONLY DO THIS FOR ADMIN
                    if( $pagenow == 'index.php' ) {  // PRINTS ON DASHBOARD
                        echo '<pre>'; print_r( $menu ); echo '</pre>'; // TOP LEVEL MENUS
                        echo '<pre>'; print_r( $submenu ); echo '</pre>'; // SUBMENUS
                    }
                }
            }
            add_action( 'admin_notices', 'debug_admin_menus' );
        endif;
    }
    function disable_plugin_deactivation_edit(){
        add_filter( 'plugin_action_links', array($this,'disable_plugin_deactivation'), 10, 4 );
    }
    /**
     * مقادیر زیر باید قبل از فراخوانی تابع تنظیم شود
     * $cw->newdashbordwidget=array('id'=>'pg_dashboard_widget', 'name'=>'pg Dashboard Widget','content'=>'Hello World, I\'m a great Dashboard Widget');
     */
    function create_dashboard_widget(){
        add_action('wp_dashboard_setup', array($this,'pg_add_dashboard_widgets'));
    }
    function noUpdateNotification(){
        add_action('admin_notices', array($this,'no_update_notification'), 1);
    }
    /**
     * باید قبل از فراخوانی این تابع به متغیر زیر مقداری از صفر تا هفت را اختصاص داد
     * admincolor
     */
    function admin_color(){
        add_filter('get_user_option_admin_color', array($this,'change_admin_color'));
    }
    /**
     *قبل از فراخوانی این تابع باید آرایه زیر مقدار دهی شود
     * $cw->newmenutext=array('main'=>__('Articles','PG-MA'),'sub1'=>__('Articles','PG-MA'),'sub2'=>__('Add Articles','PG-MA'))
     */
    function new_post_menu_text(){
        add_action( 'admin_menu', array($this,'change_post_menu_text' ));
    }
    /**
     * قبل از فراخوانی این تابع باید آرایه زیر مقدار دهی شود
     * $cw->newlabel=array('name'=>__('Articles','PG-MA'),'add_new'=>__('Add Articles','PG-MA'),
    'edit_item'=>__('Edit Articles','PG-MA'),'view_item'=>__('View Articles','PG-MA'),
    'search_items'=>__('Search Articles','PG-MA'),'not_found'=>__('No Articles found','PG-MA'),
    'not_found_in_trash'=>__('No Articles found in Trash','PG-MA'));
     */
    function new_post_type_label(){
        add_action( 'init', array($this,'change_post_type_labels') );
    }
    /**
     * قبل از فراخوانی این تابع باید آرایه زیر مقدار دهی شود
     * $cw->newadminbarlink=array('id'=>'id_mylink',
    'title'=>'طراحی و اجرا: پدیده گستر ساویس',
    'href'=>'http://www.jamal13647850.com');
     * 
     */
    function add_new_adminbar_link(){
        add_action('admin_bar_menu', array($this,'new_admin_bar_link'),100);
    }

}