<?php
/**
 * Created by PhpStorm.
 * User: Jamal
 * Date: 11/3/2016
 * Time: 10:54 PM
 */

namespace pgsavis;


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
    public function isLocally(){
        if($_SERVER['REMOTE_ADDR']=='127.0.0.1' || $_SERVER['REMOTE_ADDR']=='::1'){
            return true;
        }
        else{
            return false;
        }
    }


}