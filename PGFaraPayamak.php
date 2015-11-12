<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
class PGFaraPayamak {
    private $vars = array();
    private $WSDL = "http://87.107.121.54/post/send.asmx?wsdl";
    private $client;
    public function __construct($param) {
        // turn off the WSDL cache
        ini_set("soap.wsdl_cache_enabled", "0");
        try {
            $this->client = new SoapClient($this->WSDL);
        }
        catch (SoapFault $ex) {
            echo $ex->faultstring;
        }
        /**
         *
         * $parameters['username'] = "9211300158";
        $parameters['password'] = "15";
        $parameters['from'] = "50005000188988";
         */
        isset($param['username'])?$this->vars['username'] = $param['username']:$this->vars['username']='';
        isset($param['password'])?$this->vars['password'] = $param['password']:$this->vars['password']='';
        isset($param['from'])?$this->vars['from'] = $param['from']:$this->vars['from']='';
    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function __call($name, $arguments){
    }

    public function SendSms($to=array(),$text,$isflash=true){
        try {
            $status='';
            $parameters['username'] = $this->vars['username'];
            $parameters['password'] = $this->vars['password'];
            $parameters['from'] = $this->vars['from'];
            $parameters['to'] = $to;
            $parameters['text'] =$text;
            $parameters['isflash'] = $isflash;
            $parameters['udh'] = "";
            $parameters['recId'] = array(0);
            $parameters['status'] = 0x0;
            $this->client->SendSms($parameters)->SendSmsResult;
            return $status;
        }
        catch (SoapFault $ex) {
            echo $ex->faultstring;
        }
    }
    public function GetCredit(){
        try {
            $credit=$this->client->GetCredit(array("username"=>$this->vars['username'],"password"=>$this->vars['password']))->GetCreditResult;
            return $credit;
        }
        catch (SoapFault $ex) {
            echo $ex->faultstring;
        }
    }

}