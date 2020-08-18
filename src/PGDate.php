<?php
/**
 *@author Sayyed Jamal Ghasemi <https://www.linkedin.com/in/jamal1364/>
 *@version 1.0.0
 *
 */
namespace jamal13647850\mycustomcodes;
use DateInterval;
use DateTime;

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
    public function isDateTodayYesterdayTomorrow($date){


        $today = new DateTime(); // This object represents current date/time
        $today->format('Y-m-d H:i:s');
        $today->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison


        $match_date= new DateTime($date);
        $match_date->format('Y-m-d H:i:s');
        $match_date->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison

        $diff = $today->diff( $match_date );
        $diffDays = (integer)$diff->format( "%R%a" ); // Extract days count in interval

        switch( $diffDays ) {
            case 0:
                return "Today";
                break;
            case -1:
                return "Yesterday";
                break;
            case +1:
                return "Tomorrow";
                break;
            default:
                return "Sometime";
        }
    }
    public function isDateBitweenStartDateAndEndDate($startDate,$date,$endDate){
        if(strtotime($date)>=strtotime($startDate) && strtotime($date)<=strtotime($endDate)){
            return true;
        }
        else{
            return false;
        }
    }
    public function addDayToDate(DateTime $date,$day){
        $date->add(new DateInterval('P'.$day.'D'));
        return $date ;
    }
    public function minusDayFromDate(DateTime $date,$day){
        $date->sub(new DateInterval('P'.$day.'D'));
        return $date ;
    }
    public function addSecondToDate(DateTime $date,$seconds){
        $date->add(new DateInterval('PT'.$seconds.'S'));
        return $date;
    }
}

