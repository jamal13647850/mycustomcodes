<?php
/**
 *@author Sayyed Jamal Ghasemi <https://www.linkedin.com/in/jamal1364/>
 * Date: 11/3/2016
 * Time: 10:54 PM
 */

namespace pgsavis\mycustomcodes;


use finfo;


class PGFunctions{


    public function redirect($url){
        echo "<meta http-equiv='Refresh' content='0;URL=$url'>";
        //wp_redirect($url);
        exit();
    }
    public function redirectTwo($url){
        wp_redirect($url);
        exit();
    }
    public function redirectThree($url){
        ?>
        <script>
            window.location.href = <?php echo $url; ?>;
        </script>
        <?php
        exit();
    }
    public function redirectFour($url){
        header('Location: '.$url);
        exit();
    }
    public function isLocally(){
        if($_SERVER['REMOTE_ADDR']=='127.0.0.1' || $_SERVER['REMOTE_ADDR']=='::1'){
            return true;
        }
        else{
            return false;
        }
    }
    public static function getMimeContenttype($filename) {
        $result = new finfo();

        if (is_resource($result) === true) {
            return $result->file($filename, FILEINFO_MIME_TYPE);
        }

        return false;
    }
    public function PGHash($start,$len = 5){
        return $start.substr(md5(uniqid(rand(), true)),0,$len);
    }



    public function getFileSizeByUrl($url){
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);

        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);

        return $size;
    }

    function myLoginRedirect( $redirect_to, $request, $user ) {
        if ( isset( $user->roles ) && is_array( $user->roles ) ) {
            //check for admins
            if ( in_array( 'crm', $user->roles ) ) {
                if($this->isLocally()){
                    return "http://localhost/sadir/wp-admin/admin.php?page=crmmenu";
                }
                else{
                    return "https://gosafir.com/safir/wp-admin/admin.php?page=crmmenu";
                }

            }
        }
        return $redirect_to;
    }
    function CheckNationalCode($code){
        if($code=="0700256861")
            return true;
        if(strlen($code) <> 10){
            return false;
        }
        else {
            $codeArray = str_split($code);
            $AllEq=false;
            foreach($codeArray as $item => $value) {
                if($codeArray[0] <> $value)
                {
                    $AllEq = false;
                    break;
                }
                else{
                    $AllEq = true;
                }
            }
            if($AllEq == true) return false;
            $j = 10;
            $sum = 0;
            for($i=0; $i<=8; $i++) {
                $sum +=((int)($codeArray[$i])) * $j;
                --$j;
            }
            $divid = $sum % 11;
            if ($divid <= 2) {
                if($codeArray[9]  == $divid)
                {
                    return true;
                }
                $divid1 = 11 - $divid;
                if ($codeArray[9]  == $divid1) {
                    return true;
                }
                return false;
            }
            else {
                $divid1 = 11 - $divid;
                if ($codeArray[9]  == $divid1) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
    }
    /**
     * @param $slug
     * @param $title
     * @param $content
     * @param int $author
     * @param int $menu_order
     * @return int|WP_Error
     */
    function createNewPage($slug,$title,$content,$author=1,$menu_order=1){
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

    public static function rialTomanConverter($price,$to="rial"){
        $res=0;
        switch ($to){
            case "rial":
                $res=$price*10;
                break;
            case "toman":
                $res=round($price/10);
                break;
        }
        return $res;
    }


    private function replace_unicode_escape_sequence($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
    }
    public function unicodeDecode($str) {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
    }



    public function removeSingleQoute($string){
        $string =  str_replace("'","",$string);
        $string =  str_replace(".","",$string);
        return str_replace("’","",$string);
    }
    public function specialCheckString($strWithSingleQoute,$strWithoutSingleQoute){

        if($this->removeSingleQoute($strWithSingleQoute)=== $strWithoutSingleQoute){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Reads the requested portion of a file and sends its contents to the client with the appropriate headers.
     *
     * This HTTP_RANGE compatible read file function is necessary for allowing streaming media to be skipped around in.
     *
     * @param string $location
     * @param string $filename
     * @param string $mimeType
     * @return void
     *
     */
    function smartReadFile($location, $filename, $mimeType = 'application/octet-stream')
    {
        if (!file_exists($location))
        {
            header ("HTTP/1.1 404 Not Found");
            return;
        }

        $size	= filesize($location);
        $time	= date('r', filemtime($location));

        $fm		= @fopen($location, 'rb');
        if (!$fm)
        {
            header ("HTTP/1.1 505 Internal server error");
            return;
        }

        $begin	= 0;
        $end	= $size - 1;

        if (isset($_SERVER['HTTP_RANGE']))
        {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
            {
                $begin	= intval($matches[1]);
                if (!empty($matches[2]))
                {
                    $end	= intval($matches[2]);
                }
            }
        }

        if (isset($_SERVER['HTTP_RANGE']))
        {
            header('HTTP/1.1 206 Partial Content');
        }
        else
        {
            header('HTTP/1.1 200 OK');
        }

        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . (($end - $begin) + 1));
        if (isset($_SERVER['HTTP_RANGE']))
        {
            header("Content-Range: bytes $begin-$end/$size");
        }
        header("Content-Disposition: inline; filename=$filename");
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: $time");

        $cur	= $begin;
        fseek($fm, $begin, 0);




        while(!feof($fm) && $cur <= $end && (connection_status() == 0))
        {
            print fread($fm, min(1024 * 16, ($end - $cur) + 1));
            $cur += 1024 * 16;
        }
    }


    function _remove_script_version( $src ){
        $parts = explode( '?', $src );
        return $parts[0];
    }

    public function log($text,$data=[],$loggerName='gosafir',$path=gosafir_DIR.'/safir.log',$addMethod='debug'){
        $log = new Logger($loggerName);
        $log::setTimezone(new \DateTimeZone("Asia/Tehran"));
        $log->pushHandler(new StreamHandler($path, Logger::DEBUG));
        $log->$addMethod($text,$data);
        unset($log);
    }
}