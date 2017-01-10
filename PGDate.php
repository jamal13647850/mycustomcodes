<?php
/**
 * Created by Sayyed jamal ghasemi.
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
 namespace pgsavis;
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
    function difference_between_date($date1,$date2,$type='all'){
        $diff = abs(strtotime($date2) - strtotime($date1));
        if ($type=='all'){
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            return array('years'=>$years,'months'=>$months,'days'=>$days);
        }
        else{
            return $diff;
        }

    }

    public function day_to_second($day){
        return $day*86400;
    }
    public function month_to_day($month){
        return $month*30.4368499;
    }
    public function month_to_second($month){
        return intval ($this->month_to_day($month))*86400;
    }
    public function year_to_month($year){
        return $year*12;
    }
    public function year_to_second($year){
        return $this->month_to_second($this->year_to_month($year));
    }
    public function hour_to_second($hour){
        return $hour*3600;
    }
    public function minute_to_second($minute){
        return $minute*60;
    }
}