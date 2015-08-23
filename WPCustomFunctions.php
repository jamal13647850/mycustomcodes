<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
class WPCustomFunctions {
    private $vars = array();
    public function __construct($param) {
        $this->vars['DirPath'] =plugin_dir_path( __FILE__ ) ;
        $this->vars['UrlPath'] =plugin_dir_url( __FILE__ ) ;
        $this->vars['IncDir'] =trailingslashit( $this->vars['DirPath'] . 'inc' ) ;
        $this->vars['IncUrl'] =trailingslashit( $this->vars['UrlPath'] . 'inc' ) ;
        $this->vars['CssDir'] =trailingslashit( $this->vars['DirPath'] . 'css' ) ;
        $this->vars['CssUrl'] =trailingslashit( $this->vars['UrlPath'] . 'css' ) ;
        $this->vars['JsDir'] =trailingslashit( $this->vars['DirPath'] . 'js' ) ;
        $this->vars['JsUrl'] =trailingslashit( $this->vars['UrlPath'] . 'js' ) ;
        $this->vars['ImgDir'] =trailingslashit( $this->vars['DirPath'] . 'img' ) ;
        $this->vars['ImgUrl'] =trailingslashit( $this->vars['UrlPath'] . 'img' ) ;
        $this->vars['styles']=array();
        $this->vars['scripts']=array();
        $this->vars['localized']=array();
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
     * @param $count count of recent post for show
     */
    function show_recent_post_header($count){
        global $data;
        $the_query = new WP_Query( 'showposts='.$count );
        ?>
        <ul id="webticker2">
            <li id='item1'>
                <?php
                while ($the_query -> have_posts()) : $the_query -> the_post(); ?>
                    <i class="fa <?php echo $data['icon_seperator'] ?>"></i>
                    <a target="_blank" href="<?php the_permalink() ?>">
                        <?php the_title(); ?>
                    </a>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </li>
        </ul>
    <?php
    }

    /**
     * @param $length length of excerp
     */
    function print_excerpt($length) { // Max excerpt length. Length is set in characters
        global $post;
        $text = $post->post_excerpt;
        if ( '' == $text ) {
            $text = get_the_content('');
            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]>', $text);
        }
        $text = strip_shortcodes($text); // optional, recommended
        $text = strip_tags($text); // use ' $text = strip_tags($text,'<p><a>'); ' if you want to keep some tags

        $text = mb_substr($text,0,$length, "utf-8");
        $excerpt = reverse_strrchr($text, '.', 1);
        if( $excerpt ) {
            echo apply_filters('the_excerpt',$excerpt);
        } else {
            echo apply_filters('the_excerpt',$text);
        }
    }

    /**
     * @param $postID
     * @return string
     * get coutn of post views
     */
    function getPostViews($postID){
        $count_key = 'post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if($count==''){
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
            return "0";
        }
        return $count.' ';
    }

    /**
     * @param $postID
     * set count of views for a post
     */
    function setPostViews($postID) {
        $count_key = 'post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if($count==''){
            $count = 0;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
        }else{
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
    }

    /**
     * @param $postID
     * set start Post Views
     */
    function setstartPostViews($postID) {
        $count_key = 'post_views_count';
        $count = 35000;
        if($count==''){
            $count = 0;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
        }else{
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
    }

    /**
     * @param $num count of popular post to show
     * get popular post by views
     */
    function popularPosts($num) {
        $popular="";
        $popularpost = new WP_Query( array( 'posts_per_page' => $num,
            'meta_key' => 'post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC'  ) );
        while ( $popularpost->have_posts() ) : $popularpost->the_post();
            ?>
            <li class="rpl" style='list-style: outside none none;'>

                <a class="rpa" href="<?php the_permalink() ?>" title="<?php the_title() ?>">
                    <?php the_title() ?></a>
            </li>
        <?php
        endwhile;
    }
    /**
     * @param $num count of popular post to show
     * @param $cpt custom post types
     * get popular post by views for custom post type
     */
    function cpt_popularPosts($num,$cpt) {
        $popular="";
        $cptpopularpost = new WP_Query( array(
            'post_type'=>$cpt,
            'posts_per_page' => $num,

            'meta_key' => 'post_views_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        ) );
        while ( $cptpopularpost->have_posts() ) : $cptpopularpost->the_post();
            ?>
            <li class="rpl" style='list-style: outside none none;'>

                <a class="rpa" href="<?php the_permalink() ?>" title="<?php the_title() ?>">
                    <?php the_title() ?></a>
            </li>
        <?php
        endwhile;
    }
    /**
     * @param $catexc array of category for exclude
     * exclude some category of post only for output
     * only use in post loop
     */
    function CatExclude($catexc){
        $i = 0;
        $len = count(get_the_category());
        foreach((get_the_category()) as $category) {
            if ($i != $len - 1) {
                if (!in_array("$category->cat_name", $catexc)) {
                    echo ', '.'<a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s","vai" ), $category->name ) . '" ' . '>' . $category->name.'</a> ';
                }
            }
            else if ($i == $len - 1) {
                if (!in_array("$category->cat_name", $catexc)) {
                    echo ', '.'<a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s","vai" ), $category->name ) . '" ' . '>' . $category->name.'</a> ';
                }
            }
            else if ($i == 0){
                if (!in_array("$category->cat_name", $catexc)) {
                    echo '<a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s","vai" ), $category->name ) . '" ' . '>' . $category->name.'</a> ';
                }
            }
            $i++;
        }
    }

    /**
     * @param string $pages
     * @param int $range
     * create pagination for pages.
     */
    function mw_pagination($pages = '', $range = 5){
        $showitems = ($range * 2)+1;
        global $paged;
        if(empty($paged)){
            $paged = 1;
        }
        if($pages == ''){
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if(!$pages){
                $pages = 1;
            }
        }
        if(1 != $pages){
            echo  '<div class="pagination-centered">';
            echo "<ul class='pagination'>";
            if($paged > 2 && $paged > $range+1 && $showitems < $pages){
                echo "<li class='arrow'><a href='".get_pagenum_link(1)."'>&laquo;</a></li>";
            }
            if($paged > 1 && $showitems < $pages){
                echo "<li class='arrow'><a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a></li>";
            }
            for ($i=1; $i <= $pages; $i++){
                if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){
                    echo ($paged == $i)? "<li class='current'>".$i."</li>":"<li><a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a></li>";
                }
            }
            if ($paged < $pages && $showitems < $pages){
                echo "<li class='arrow'><a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a></li>";
            }
            if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages){
                echo "<li class='arrow'><a href='".get_pagenum_link($pages)."'>&raquo;</a></li>";
                echo "</ul>\n";
            }
            echo  "</div>";
        }

    }

    /**
     * Remove version from all stylee and scripts in header
     */
    function RemoveVersion(){
        add_filter( 'style_loader_src', array($this,'t5_remove_version') );
        add_filter( 'script_loader_src', array($this,'t5_remove_version') );
        function t5_remove_version( $url )
        {
            return remove_query_arg( 'ver', $url );
        }
    }

    /**
     * @param string $handle
     * @param string $src
     * @param array $deps
     * @param bool $ver
     * @param string $media
     * @param string $admin_frontend
     * add css file
     */
    function AddStyle($handle='None', $src='None', $deps=array() , $ver=false , $media='all',$admin_frontend='admin'){
        $newfield=array('handle'=>$handle,'src'=>$src,'deps'=>$deps,'ver'=>$ver,'media'=>$media,'admin_frontend'=>$admin_frontend);
        array_push($this->vars['styles'],$newfield);
    }

    /**
     * @param string $handle
     * @param string $src
     * @param array $deps
     * @param bool $ver
     * @param bool $in_footer
     * @param string $admin_frontend
     * add js file
     */
    function AddScript($handle='None', $src='None', $deps=array() , $ver=false , $in_footer=false,$admin_frontend='admin'){
        $newfield=array('handle'=>$handle,'src'=>$src,'deps'=>$deps,'ver'=>$ver,'in_footer'=>$in_footer,'admin_frontend'=>$admin_frontend);
        array_push($this->vars['scripts'],$newfield);
    }
    /**
     * register added css and js files
     */
    function RegisterStyles_Scripts(){
        foreach($this->vars['styles'] as $field){
            if (!is_admin() & $field['admin_frontend']=='frontend') {
                wp_register_style($field['handle'],$field['src'],$field['deps'],$field['ver'],$field['media']);
                wp_enqueue_style($field['handle']);
            }
            else{
                wp_register_style($field['handle'],$field['src'],$field['deps'],$field['ver'],$field['media']);
                wp_enqueue_style($field['handle']);
            }

        }
        foreach($this->vars['scripts'] as $field){
            if (!is_admin() & $field['admin_frontend']=='frontend') {
                wp_enqueue_script( $field['handle'],$field['src'],$field['deps'],$field['ver'],$field['in_footer'] );
            }
            else{
                wp_enqueue_script( $field['handle'],$field['src'],$field['deps'],$field['ver'],$field['in_footer']);
            }

        }
    }
    /**
     * @param string $handle
     * @param string $name
     * @param string $data
     * @param string $admin_frontend
     * add localized for script
     */
    function AddScriptlocalized($handle='None', $name='None', $data='None' ,$admin_frontend='admin'){
        $newfield=array('handle'=>$handle,'name'=>$name,'data'=>$data,'admin_frontend'=>$admin_frontend);
        array_push($this->vars['localized'],$newfield);
    }
    /**
     * send localized value
     */
    function send_localized(){
        foreach($this->vars['localized'] as $field){
            if (!is_admin() & $field['admin_frontend']=='frontend') {
                wp_localize_script( $field['handle'],$field['name'], $field['data'] );
            }
            else{
                wp_localize_script( $field['handle'],$field['name'], $field['data'] );
            }
        }
    }
    /**
     * *********************************
     */
    function admin_inline_js(){
        echo $this->vars['admin_inline_styles_script'];
    }
    function front_inline_js(){
        echo $this->vars['inline_styles_script'];
    }
    /**
     * add inline script and style in admin footer
     */
    function Addinline_Admin_scripts(){
        add_action( 'admin_footer', array($this,'admin_inline_js') );
    }
    /**
     * add inline script and style in frontend footer
     */
    function Addinline_frontend_scripts(){
        add_action( 'wp_footer', array($this,'front_inline_js') );
    }
    /**
    *$post_id - The ID of the post you'd like to change.
    *$status -  The post status publish|pending|draft|private|static|object|attachment|inherit|future|trash.
    */
    function change_post_status($post_id,$status){
        $current_post = get_post( $post_id, 'ARRAY_A' );
        $current_post['post_status'] = $status;
        wp_update_post($current_post);
    }

    /**
     * @param $message
     * alert in php
     */
    function alert($message){
        echo "<script type='text/javascript'>alert('$message');</script>";
    }

    /**
     * @param $query
     * @return mixed
     * use show_cpt_in_archivetag() for show custom post type in archive and tag page
     * before you should set $this->vars['show_cpt'] is an array of cpt $this->vars['show_cpt']=array('aD','ada')
     */
    function query_post_type($query) {
        if(is_category() || is_tag()) {
            $post_type = get_query_var('post_type');
            if($post_type)
                $post_type = $post_type;
            else
                $post_type =array_merge(array('post'),$this->vars['show_cpt']); // replace cpt to your custom post type
            $query->set('post_type',$post_type);
            return $query;
        }
    }
    function show_cpt_in_archivetag(){
        add_filter('pre_get_posts', array($this,'query_post_type'));
    }

    /**
     * @return string
     * get ip of user
     */
    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    /**
     * @param $data
     * Send debug code to the Javascript console
     */
    function debug_to_console($data) {
        if(is_array($data) || is_object($data))
        {
            echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
        } else {
            echo("<script>console.log('PHP: ".$data."');</script>");
        }
    }
    /**
	* 
	* @param undefined $files this is $_files
	* @param undefined $post_id
	* 
	* @return
	*/
    function insert_thumbnail_to_post($files,$post_id,$default_thumb){
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
	                if(wp_mkdir_p($upload_dir['path']))
	                    $filet = $upload_dir['path'] . '/' . $filename;
	                else
	                    $filet = $upload_dir['basedir'] . '/' . $filename;
	                file_put_contents($filet, $image_data);
	                $wp_filetype = wp_check_filetype($filename, null );
	                $attachment = array(
	                    'post_mime_type' => $wp_filetype['type'],
	                    'post_title' => sanitize_file_name($filename),
	                    'post_content' => '',
	                    'post_status' => 'inherit'
	                );
	                $attach_id = wp_insert_attachment( $attachment, $filet, $post_id );
	                $attach_data = wp_generate_attachment_metadata( $attach_id, $filet );
	                wp_update_attachment_metadata( $attach_id, $attach_data );
	                //set_post_thumbnail( $post_id, $attach_id );
	            }
	            else{
	                $attach_id = media_handle_upload( $file, $post_id );
	            }
	        }
	    }
	    update_post_meta($post_id,'_thumbnail_id',$attach_id);

	}
	/**
	* 
	* @param undefined $image
	* @param undefined $post_id
	* @param undefined $thumbid
	* 
	* @return
	*/
	function insert_custom_thumbnail($image,$post_id,$thumbid){
	    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	                $upload_dir = wp_upload_dir();
	                $image_data = file_get_contents($image);
	                $filename = basename($image);
	                if(wp_mkdir_p($upload_dir['path']))
	                    $filet = $upload_dir['path'] . '/' . $filename;
	                else
	                    $filet = $upload_dir['basedir'] . '/' . $filename;
	                file_put_contents($filet, $image_data);
	                $wp_filetype = wp_check_filetype($filename, null );
	                $attachment = array(
	                    'post_mime_type' => $wp_filetype['type'],
	                    'post_title' => sanitize_file_name($filename),
	                    'post_content' => '',
	                    'post_status' => 'inherit'
	                );
	                $attach_id = wp_insert_attachment( $attachment, $filet, $post_id );
	                $attach_data = wp_generate_attachment_metadata( $attach_id, $filet );
	                wp_update_attachment_metadata( $attach_id, $attach_data );
	                
	            
	            
	        
	    
	    update_post_meta($post_id,'_thumbnail_id_2',$attach_id);

	}
} 