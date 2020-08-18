<?php
/**
 *@author Sayyed Jamal Ghasemi <https://www.linkedin.com/in/jamal1364/>
 *@version 1.0.0
 *
 */
namespace jamal13647850\mycustomcodes;
use SoapClient;
use SoapFault;

class PGFaraPayamak {
    private $vars = array();
    private $WSDL = "http://87.107.121.54/post/send.asmx?wsdl";
    private $error;
    private $client;
    public function __construct($param) {
        // turn off the WSDL cache
        ini_set("soap.wsdl_cache_enabled", "0");
        try {
            $this->client = new SoapClient($this->WSDL);
        }
        catch (SoapFault $ex) {
            $this->error= $ex->faultstring;
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
        add_action( 'admin_bar_menu', [$this,'addSmsCreditToAdminBar'], 100 );
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
        if($this->checkWsdlIsOnline()){
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
                return $ex->faultstring;
            }
        }
    }
    public function GetCredit(){
        if($this->checkWsdlIsOnline()){
            try {
                $credit=$this->client->GetCredit(array("username"=>$this->vars['username'],"password"=>$this->vars['password']))->GetCreditResult;
                return $credit;
            }
            catch (SoapFault $ex) {
                return $ex->faultstring;
            }
        }

    }
    public function addSmsCreditToAdminBar( $wp_admin_bar ) {
        $args = array(
            'id'    => 'smsCredit',
            'title' => $this->GetCredit(),
            'meta'  => array( 'class' => 'sms-toolbar-page' )
        );
        $wp_admin_bar->add_node( $args );
    }
    private function checkWsdlIsOnline(){
        $handle = curl_init($this->WSDL);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        preg_match("/SOAP-ERROR/", $this->error, $output_array);
        if(count($output_array)){
            return false;
        }
        else{
            return true;
        }
    }


}