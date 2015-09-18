<?php
/**
 * Created by Sayyed jamal ghasemi.
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
require_once('HelperFunctions.php');
class WPCustomFunctions extends HelperFunctions {
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
     * @param $object
     * @return array
     */
    function objectToArray( $object ){
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        return array_map( 'objectToArray', $object );
    }
    /**
     * @return mixed
     */
    function get_user_role() {
        global $current_user;

        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);

        return $user_role;
    }

    /**
     * @param $slug
     * @param $title
     * @param $content
     * @param int $author
     * @param int $menu_order
     * @return int|WP_Error
     */
    function create_new_page($slug,$title,$content,$author=1,$menu_order=1){
        $new_page_id = wp_insert_post( array(
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

    /**
     * @param $userid
     * @param string $post_type
     * @return mixed
     */
    function count_user_posts_by_type( $userid, $post_type = 'post' ) {
        global $wpdb;
        $where = get_posts_by_author_sql( $post_type, true, $userid );
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );
        return apply_filters( 'get_usernumposts', $count, $userid );
    }
    function add_default_content($cont,$post_type='post'){
        $this->vars['editorcontent'] = $cont;
        $this->vars['posttype'] = $post_type;
        add_filter( 'default_content', array($this,'my_editor_content'), 10, 2 );
        function my_editor_content( $content, $post ) {
            switch( $post->post_type ) {
                case $this->vars['posttype']:
                    $content = $this->vars['editorcontent'];
                    break;
            }
            return $content;
        }
    }
    /**
     * add new column to post and cpt in admin
     * $ppp=new WPCustomFunctions(array());
     *  $ppp->add_new_column('cpp','new header',array(array('postid'=>1,'value'=>'testvalue'),array('postid'=>2,'value'=>'sva')));
     */
    function add_new_column($posttype,$colum_name,$column_header,$content=array()){
        $this->myvars['column_name'] = $colum_name;
        $this->myvars['column_header']=$column_header;
        $this->myvars['content']=$content;
        switch ($posttype){
            case 'all':
                $headerhook='manage_posts_columns';
                $contenthook='manage_posts_custom_column';
                break;
            default:
                $headerhook='manage_'.$posttype.'_posts_columns';
                $contenthook='manage_'.$posttype.'_posts_custom_column';
                break;
        }
        add_action($contenthook, array($this,'ST4_columns_content'), 10, 2);
        add_filter($headerhook, array($this,'ST4_columns_head'));
    }

    /**
     * remove column from a post or cpt in admin
     * @param $defaults
     * @return mixed
     */
    function remove_column($posttype,$remove_colum_name){
        $this->myvars['remove_column_name'] = $remove_colum_name;
        $hook='manage_'.$posttype.'_posts_columns';
        add_filter($hook, array($this,'ST4_columns_remove_column'),1000);
    }

    /**
     * add facebook and twitter share button to rss
     * @param string $facebookicon
     * @param string $twitericon
     * @example
     * $ppp->add_social_share_rss(cofegame_class_URL.'img/facebook.jpg',cofegame_class_URL.'img/Twitter.png');
     */
    function add_social_share_rss($facebookicon='./img/facebook.jpg',$twitericon='./img/Twitter.png'){
        $this->myvars['facebook_icon_url']=$facebookicon;;
        $this->myvars['twitter_icon_url']=$twitericon;
        add_filter('the_excerpt_rss', array($this,'wpb_add_feed_content'));
        add_filter('the_content', array($this,'wpb_add_feed_content'));
    }

    /**
     * hid plugin meta line in plugins screen
     */
    function hide_plugin_meta(){
        add_filter( 'plugin_row_meta', array($this,'range_plu_plugin_meta'), 10, 2 );
    }

    /**
     * hide all update notification in wordpress
     */
    function hide_update(){
        add_filter('pre_site_transient_update_core',array($this,'remove_core_updates'));
        add_filter('pre_site_transient_update_plugins',array($this,'remove_core_updates'));
        add_filter('pre_site_transient_update_themes',array($this,'remove_core_updates'));
        remove_action('load-update-core.php','wp_update_plugins');
        add_filter('pre_option_update_core','__return_null');
    }

    /**
     * Remove edit link for all plugins and Remove deactivate link for important plugins
     */
    function disable_plugin_deactivation_edit(){
        add_filter( 'plugin_action_links', array($this,'disable_plugin_deactivation'), 10, 4 );
    }

    /**
     * change text of footer in admin panel
     * @param $text string
     */
    function change_footer_text($text){
        $this->myvars['footercontent']=$text;
        add_filter('admin_footer_text', array($this,'change_footer_content'),9999,1);
    }
    /**
     * change text of  version in footer in admin panel
     * @param $text string
     */
    function change_footer_ver($text=''){
        $this->myvars['footerversion']=$text;
        add_filter( 'update_footer', array($this,'change_footer_version'), 9999);
    }
    /**
     * remove admin menu
     */
    function remove_admin_menu(){
        add_action('admin_menu', array($this,'remove_menu_elements'), 999);
    }
    /**
     * $html = '<p class="description">';
     * $html .= 'Upload your PDF here.';
     * $html .= '</p>';
     * $html .= '<input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="" size="25" />';
     *
     * save_custom_meta_data($post_id,'passportimagead' );
     *
     * $imgurl=get_post_meta ( $post_id, 'passportimagead')['0']['url'];
     *$imgtag="<img width='200' height='200' src='$imgurl' >";
     *
     * @param $postid
     * @param $metaid
     * @param array $supported_types
     */
    function save_custom_meta_data($postid,$metaid,$supported_types = array('image/png','image/jpeg','image/bmp','image/gif')) {
        // Make sure the file array isn't empty
        if(!empty($_FILES[$metaid]['name'])) {
            // Get the file type of the upload
            $arr_file_type = wp_check_filetype(basename($_FILES[$metaid]['name']));
            $uploaded_type = $arr_file_type['type'];
            // Check if the type is supported. If not, throw an error.
            if(in_array($uploaded_type, $supported_types)) {
                // Use the WordPress API to upload the file
                $upload = wp_upload_bits($_FILES[$metaid]['name'], null, file_get_contents($_FILES[$metaid]['tmp_name']));
                if(isset($upload['error']) && $upload['error'] != 0) {
                    wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                } else {
                    add_post_meta($postid, $metaid, $upload);
                    update_post_meta($postid, $metaid, $upload);
                } // end if/else
            } else {
                wp_die("The file type that you've uploaded is not Allowed.");
            } // end if/else

        } // end if
    }

    /**
     * add title to wp_query
     * $hotel_args = array (
     *'post_type'              => 'hotel',
     *'posts_per_page'         => 300,
     *'post_status'            => 'Publish',
     *'post_title_like'=>$hotel_name,
     *'meta_query'             => array(
     *array(
     *'key'       => 'cities',
     *'value'     => $city,
     *'compare'   => '=',
     *)
     *)
     *);
     */
    function add_title_to_wp_query(){
        add_filter( 'posts_where', array($this,'title_like_posts_where'), 10, 2 );
    }

} 