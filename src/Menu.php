<?php
/**
 * @author Sayyed Jamal Ghasemi <https://www.linkedin.com/in/jamal1364/>
 * Date: 03/09/2016
 * Time: 09:33 AM
 */

namespace jamal13647850\mycustomcodes;


class Menu {
    private $vars = array();
    private $results = array();
    private $currentresult;
    private $currentslug;
    private $menu_slug;
    function __construct(){
        require_once(ABSPATH . "wp-admin/includes/plugin.php");
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        $this->vars['currentresult']=$this->currentresult;
    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function AddMenuPage($page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null ){
        $this->vars['page_title'] =$page_title ;
        $this->vars['menu_title'] = $menu_title ;
        $this->vars['capability'] = $capability ;

        $this->menu_slug = $menu_slug ;
        $this->vars['function'] = $function ;
        $this->vars['icon_url'] = $icon_url ;
        $this->vars['position'] = $position ;

        return $this->InitAdminPages();
    }
    public function InitAdminPages(){
        $this->vars['currentresult']=add_menu_page($this->vars['page_title'], $this->vars['menu_title'], $this->vars['capability'], $this->getMenuSlug(),
            $this->vars['function']  , $this->vars['icon_url']  , $this->vars['position']  );
        return $this->vars['currentresult'];
    }


    public function AddSubmenuPage( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' ){
        $this->submenu=new submenu();
        $this->submenu->sub_parent_slug =$parent_slug ;
        $this->submenu->sub_page_title =$page_title ;
        $this->submenu->sub_menu_title = $menu_title ;
        $this->submenu->sub_capability = $capability ;
        $this->submenu->sub_menu_slug = $menu_slug ;
        $this->submenu->sub_function = $function ;


        return $this->submenu->InitSubAdminPages();
    }

    /**
     * @return mixed
     */
    public function getMenuSlug(){
        return $this->menu_slug;
    }
}
class submenu{
    private $vars = array();
    function __construct(){
        $this->vars['sub_currentresult']='';
    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function InitSubAdminPages(){
        return add_submenu_page($this->vars['sub_parent_slug'], $this->vars['sub_page_title'] , $this->vars['sub_menu_title'],
            $this->vars['sub_capability'], $this->vars['sub_menu_slug'], $this->vars['sub_function']);
    }
}