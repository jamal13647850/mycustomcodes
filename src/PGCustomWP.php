<?php

namespace jamal\mycustomcodes;

class PGCustomWP
{


    private string $footerContent;

    public function __construct()
    {
    }

    public function changeFooterContent(string $footerContent)
    {
        add_filter('admin_footer_text', function () use ($footerContent) {
            echo ($footerContent);
        }, 9999, 1);
    }

    public function changeFooterVersion(string $footerVersion)
    {
        add_filter('update_footer', function () use ($footerVersion) {
            return ($footerVersion);
        }, 9999, 1);
    }

    public function removeMenuElements()
    {
        add_action('admin_menu', function () {
            global $submenu, $menu;
            remove_submenu_page('themes.php', 'theme-editor.php');
            remove_submenu_page('themes.php', 'themes.php');
            unset($submenu['themes.php'][6]); // remove customize link
            unset($submenu['themes.php'][15]); // remove customize header link
            unset($menu[75]); // remove tools link
            remove_submenu_page('plugins.php', 'plugin-editor.php');
        }, 999);
    }

    public function removeDashboardWidgets()
    {
        add_action('wp_dashboard_setup', function () {
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
            remove_action('welcome_panel', 'wp_welcome_panel');
            /*remove woocomrce widget*/
            unregister_widget('WC_Widget_Recent_Products');
            unregister_widget('WC_Widget_Featured_Products');
            unregister_widget('WC_Widget_Product_Categories');
            unregister_widget('WC_Widget_Product_Tag_Cloud');
            unregister_widget('WC_Widget_Cart');
            unregister_widget('WC_Widget_Layered_Nav');
            unregister_widget('WC_Widget_Layered_Nav_Filters');
            unregister_widget('WC_Widget_Price_Filter');
            unregister_widget('WC_Widget_Product_Search');
            unregister_widget('WC_Widget_Top_Rated_Products');
            unregister_widget('WC_Widget_Recent_Reviews');
            unregister_widget('WC_Widget_Recently_Viewed');
            unregister_widget('WC_Widget_Best_Sellers');
            unregister_widget('WC_Widget_Onsale');
            unregister_widget('WC_Widget_Random_Products');
        });
    }


    public function removeAdminBarLinks()
    {
        add_action('wp_before_admin_bar_render', function () {
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
        });
    }

    public function hideDashboardHelp()
    {
        add_action('admin_head', function () {
            echo '<style type="text/css">
                    #contextual-help-link-wrap { display: none !important; }
                 </style>';
        });
    }

    public function customizeLoginPage($logo)
    {
        add_action('login_head', function () use ($logo) {
            echo '<style type="text/css">
                h1 a { 
			background-image:url(' . $logo . ') !important; 
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
        });
    }

    public function changeLoginTitle()
    {
        add_filter('login_headertitle', function () {
            return get_bloginfo();
        }, 9999);
    }

    public function setMailFrom($new)
    {
        add_filter('wp_mail_from', function ($old) use ($new) {
            return ($new);
        });
    }
    public function setMailFromName($new)
    {
        add_filter('wp_mail_from_name', function ($old) use ($new) {
            return ($new);
        });
    }

    public function removeWPGeneratore()
    {
        remove_action('wp_head', 'wp_generator');
    }

    public function removeAdminBar()
    {
        add_filter('show_admin_bar', '__return_false');
    }

    public function AddChangeMeidaLibraryColumns(array $col)
    {
        add_filter('manage_media_columns', function ($cols) use ($col) {
            //$cols["media_url"] = "URL";
            return array_merge($cols, $col);
        });
    }

    public function setMediaLibraryColumnContent($columnName, $function)
    {
        add_action('manage_media_custom_column', function ($column_name, $id) use ($columnName, $function) {
            if ($column_name == $columnName)  $function($id);
        }, 10, 2);
    }


    public function debugMenu()
    {
        if (!function_exists('debug_admin_menus')) :
            function debug_admin_menus()
            {
                global $submenu, $menu, $pagenow;
                if (current_user_can('manage_options')) { // ONLY DO THIS FOR ADMIN
                    if ($pagenow == 'index.php') {  // PRINTS ON DASHBOARD
                        echo '<pre>';
                        print_r($menu);
                        echo '</pre>'; // TOP LEVEL MENUS
                        echo '<pre>';
                        print_r($submenu);
                        echo '</pre>'; // SUBMENUS
                    }
                }
            }
            add_action('admin_notices', 'debug_admin_menus');
        endif;
    }

    public function disablePluginDeactivationEdit(array $pluginFile)
    {
        add_filter('plugin_action_links', function ($actions, $plugin_file, $plugin_data, $context) use ($pluginFile) {

            // Remove edit link for all plugins
            if (array_key_exists('edit', $actions))
                unset($actions['edit']);
            // Remove deactivate link for important plugins
            if (array_key_exists('deactivate', $actions) && in_array($plugin_file, $pluginFile))
                unset($actions['deactivate']);
            return $actions;
        }, 10, 4);
    }

    public function addDashboardWidget(string $widgetID, string $widgetTitle, $contentFunction)
    {
        add_action('wp_dashboard_setup', function () use ($widgetID, $widgetTitle, $contentFunction) {
            wp_add_dashboard_widget($widgetID, $widgetTitle, $contentFunction, null, null, 'normal', 'high');
        });
    }

    public function noUpdateNotification()
    {
        add_action('admin_notices', function () {
            remove_action('admin_notices', 'update_nag', 3);
        }, 1);
        add_filter('pre_site_transient_update_core', function () {
            global $wp_version;
            return (object) array('last_checked' => time(), 'version_checked' => $wp_version,);
        });
        add_filter('pre_site_transient_update_plugins', function () {
            global $wp_version;
            return (object) array('last_checked' => time(), 'version_checked' => $wp_version,);
        });
        add_filter('pre_site_transient_update_themes', function () {
            global $wp_version;
            return (object) array('last_checked' => time(), 'version_checked' => $wp_version,);
        });
        remove_action('load-update-core.php', 'wp_update_plugins');
        add_filter('pre_option_update_core', '__return_null');
    }


    public function setAdminColor(int $color)
    {
        add_filter('get_user_option_admin_color', function () use ($color) {
            $colors = array('default', 'ectoplasm', 'light', 'blue', 'coffee', 'midnight', 'ocean', 'sunrise');
            return $colors[$color];
        });
    }

    public function addNewAdminbarLink(string $id, string $title, string $href, $parent = false, array $meta = [])
    {
        add_action('admin_bar_menu', function ($wp_admin_bar) use ($id, $title, $href, $parent, $meta) {
            //global $wp_admin_bar;
            $wp_admin_bar->add_menu(
                array(
                    'id' => $id,
                    'title' =>  $title,
                    'href' => $href,
                    'parent' => $parent,
                    'meta' => $meta
                )
            );
        }, 100);
    }


    /**
     * @param $slug
     * @param $title
     * @param $content
     * @param int $author
     * @param int $menu_order
     * @return int|WP_Error
     */
    public function createNewPage($slug, $title, $content, $author = 1, $menu_order = 1)
    {
        $new_page_id = wp_insert_post(array(
            'post_title' => $title,
            'post_type' => 'page',
            'post_name' => $slug,
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => $content,
            'post_status' => 'publish',
            'post_author' => $author,
            'menu_order' => $menu_order
        ));
        return $new_page_id;
    }


    /**
     *
     * @param undefined $files this is $_files
     * @param undefined $post_id
     *
     * @return
     */
    public function UploadAndAssignThumbnailToPost($files, $post_id, $default_thumb)
    {
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        if ($files) {
            foreach ($files as $file => $array) {
                if ($files[$file]['error'] !== UPLOAD_ERR_OK) {
                    //echo "upload error : " . $files[$file]['error'];
                    $upload_dir = wp_upload_dir();
                    $image_data = file_get_contents($default_thumb);
                    $filename = basename($default_thumb);
                    if (wp_mkdir_p($upload_dir['path']))
                        $filet = $upload_dir['path'] . '/' . $filename;
                    else
                        $filet = $upload_dir['basedir'] . '/' . $filename;
                    file_put_contents($filet, $image_data);
                    $wp_filetype = wp_check_filetype($filename, null);
                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => sanitize_file_name($filename),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $filet, $post_id);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filet);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    //set_post_thumbnail( $post_id, $attach_id );
                } else {
                    $attach_id = media_handle_upload($file, $post_id);
                }
            }
        }
        update_post_meta($post_id, '_thumbnail_id', $attach_id);
    }

    /**
     * add new column to post and cpt in admin
     * $ppp=new WPCustomFunctions(array());
     *  $ppp->add_new_column('cpp','new header',array(array('postid'=>1,'value'=>'testvalue'),array('postid'=>2,'value'=>'sva')));
     */
    public function addNewColumn($postType, $columName, $columnHeader, $content = array())
    {

        switch ($postType) {
            case 'all':
                $headerhook = 'manage_posts_columns';
                $contenthook = 'manage_posts_custom_column';
                break;
            default:
                $headerhook = 'manage_' . $postType . '_posts_columns';
                $contenthook = 'manage_' . $postType . '_posts_custom_column';
                break;
        }
        add_action($contenthook, function ($column_name, $post_ID) use ($columName, $content) {
            if ($column_name == $columName) {
                foreach ($content as $cont) {
                    if ($cont['postid'] == $post_ID) {
                        $result = $cont['value'];
                    }
                }
                echo $result;
            }
        }, 10, 2);
        add_filter($headerhook, function ($defaults) use ($columName, $columnHeader) {
            $defaults[$columName] = $columnHeader;
            return $defaults;
        });
    }


    /**
     * remove column from a post or cpt in admin
     * @param $defaults
     * @return mixed
     */
    public function removeColumn($postType, $removeColumName)
    {

        $hook = 'manage_' . $postType . '_posts_columns';
        add_filter($hook, function ($defaults) use ($removeColumName) {
            unset($defaults[$removeColumName]);
            return $defaults;
        }, 1000);
    }

    /**
     * hid plugin meta line in plugins screen
     */
    public function hidePluginMeta()
    {
        add_filter('plugin_row_meta', function ($plugin_meta, $plugin_file) {
            return $plugin_meta;
        }, 10, 2);
    }


        /**
     * for check to run once code
     * @param $key
     * @return bool
     * usage:if (run_once('add_user_role')){
     *           //do you stuff and it will only run once
     *          }
     */
    function runOnce($key){
        $test_case = get_option('run_once');
        if (isset($test_case[$key]) && $test_case[$key]){
            return false;
        }else{
            $test_case[$key] = true;
            update_option('run_once',$test_case);
            return true;
        }
    }

    
    public function createTable(string $createQuery)
    {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($createQuery);
    }
}
