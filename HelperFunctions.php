<?php

/**
 * Created by PhpStorm.
 * User: jamal13647850
 * Date: 27/08/2015
 * Time: 09:49 AM
 */
class HelperFunctions {

    public $myvars = array();

    public function __construct($param) {
        $this->myvars['DirPath'] = plugin_dir_path(__FILE__);
        $this->myvars['UrlPath'] = plugin_dir_url(__FILE__);
        $this->myvars['IncDir'] = trailingslashit($this->myvars['DirPath'] . 'inc');
        $this->myvars['IncUrl'] = trailingslashit($this->myvars['UrlPath'] . 'inc');
        $this->myvars['CssDir'] = trailingslashit($this->myvars['DirPath'] . 'css');
        $this->myvars['CssUrl'] = trailingslashit($this->myvars['UrlPath'] . 'css');
        $this->myvars['JsDir'] = trailingslashit($this->myvars['DirPath'] . 'js');
        $this->myvars['JsUrl'] = trailingslashit($this->myvars['UrlPath'] . 'js');
        $this->myvars['ImgDir'] = trailingslashit($this->myvars['DirPath'] . 'img');
        $this->myvars['ImgUrl'] = trailingslashit($this->myvars['UrlPath'] . 'img');
        $this->myvars['styles'] = array();
        $this->myvars['scripts'] = array();
        $this->myvars['localized'] = array();
        $this->myvars['column_name']='';
        $this->myvars['facebook_icon_url']='../img/facebook.jpg';
        $this->myvars['twitter_icon_url']='../img/Twitter.png';

    }

    public function __set($name, $value) {
        $this->myvars[$name] = $value;
    }

    public function __get($name) {
        return $this->myvars[$name];
    }

    public function __call($name, $arguments) {

    }
    /**
     * @param $defaults
     * @return mixed
     * helper function for add head to column
     */
    function ST4_columns_head($defaults) {
        $defaults[$this->myvars['column_name']] = $this->myvars['column_header'];
        return $defaults;
    }

    /**
     * @param $column_name
     * @param $post_ID
     * helper function to show column content
     */
    function ST4_columns_content($column_name, $post_ID) {
        if ($column_name == $this->myvars['column_name']) {
            foreach($this->myvars['content'] as $cont){
                if($cont['postid']==$post_ID) {
                    $result=$cont['value'];
                }
            }
            echo $result;
        }
    }
    /**
     * helper function to remove column from a post or cpt in admin
     * @param $defaults
     * @return mixed
     */
    function ST4_columns_remove_column($defaults) {
        unset($defaults[$this->myvars['remove_column_name']]);
        return $defaults;
    }

    /**
     * helper for add social buttons to rss feed
     * @param $content
     * @return string
     *
     */
    function wpb_add_feed_content($content) {
        // Check if a feed is requested
        if(is_feed()) {
            // Encoding post link for sharing
            $permalink_encoded = urlencode(get_permalink());
            // Getting post title for the tweet
            $post_title = get_the_title();
            $facebook_icon_url=$this->myvars['facebook_icon_url'];
            $twitter_icon_url=$this->myvars['twitter_icon_url'];
            // Content you want to display below each post
            // This is where we will add our icons
            $content .= '<p>
                <a href="http://www.facebook.com/sharer/sharer.php?u=' . $permalink_encoded . '" title="Share on Facebook">
                    <img src="'.$facebook_icon_url.'" title="Share on Facebook" alt="Share on Facebook" width="64px" height="64px" />
                </a>
                <a href="http://www.twitter.com/share?&text='. $post_title . '&amp;url=' . $permalink_encoded . '" title="Share on Twitter">
                    <img src="'.$twitter_icon_url.'" title="Share on Twitter" alt="Share on Twitter" width="64px" height="64px" />
                </a>
            </p>';
        }
        return $content;
    }
}