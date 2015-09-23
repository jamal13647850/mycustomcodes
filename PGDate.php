<?php
/**
 * Created by Sayyed jamal ghasemi.
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
class PGDate {
    private $vars = array();
    public function __construct($param) {

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
     * return difference between two date
     * @param $date1 string
     * @param $date2 string
     * @return array array('years'=>$years,'months'=>$months,'days'=>$days)
     */
    function difference_between_date($date1,$date2){
        $diff = abs(strtotime($date2) - strtotime($date1));
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        return array('years'=>$years,'months'=>$months,'days'=>$days);
    }

}