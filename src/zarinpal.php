<?php
/**
 * @author Sayyed Jamal Ghasemi <https://www.linkedin.com/in/jamal1364/>
 * Date: 12/31/2017
 * Time: 9:08 AM
 */

namespace gosafir;


class zarinpal{
    private $MerchantID;
    private $requestUrl;
    private $verifyUrl;
    /**
     * zarinpal constructor.
     */
    public function __construct($MerchantID,$isSandBox=false){
        $this->MerchantID=$MerchantID;

        /*$isSandBox?$this->requestUrl='https://sandbox.zarinpal.com/pg/services/WebGate/wsdl':$this->requestUrl='https://www.zarinpal.com/pg/rest/WebGate/PaymentRequest.json';
        $isSandBox?$this->verifyUrl='https://sandbox.zarinpal.com/pg/services/WebGate/wsdl':$this->verifyUrl='https://www.zarinpal.com/pg/rest/WebGate/PaymentVerification.json';*/
        $isSandBox?$this->requestUrl='https://sandbox.zarinpal.com/pg/rest/WebGate/PaymentRequest.json':$this->requestUrl='https://www.zarinpal.com/pg/rest/WebGate/PaymentRequest.json';
        $isSandBox?$this->verifyUrl='https://sandbox.zarinpal.com/pg/rest/WebGate/PaymentVerification.json':$this->verifyUrl='https://www.zarinpal.com/pg/rest/WebGate/PaymentVerification.json';
        $isSandBox?$this->redirectUrl='https://sandbox.zarinpal.com/pg/StartPay/':$this->redirectUrl='https://www.zarinpal.com/pg/StartPay/';
    }

    public function request($Amount,$CallbackURL,$Description,$Email='',$Mobile=''){
        $data = array(
            'MerchantID' => $this->MerchantID,
            'Amount' => $Amount,
            'CallbackURL' => $CallbackURL,
            'Description' => $Description,
            'Email' => $Email,
            'Mobile' => $Mobile
        );

        $jsonData = json_encode($data);

        $ch = curl_init($this->requestUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true);
        curl_close($ch);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $errpr=$this->errorProcess($result["Status"]);
            if ($errpr["status"]){
                $pgf=new PGFunctions();
                $pgf->redirect($this->redirectUrl .(int)$result["Authority"]);
            }
            else {
                echo $errpr["message"];
            }
        }
    }
    public function verify($Amount){
        $Authority = $_GET['Authority'];
        $data = array(
            'MerchantID' => $this->MerchantID,
            'Authority' => $Authority,
            'Amount' => $Amount
        );
        $jsonData = json_encode($data);
        $ch = curl_init($this->verifyUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);


        if ($err) {
            $verifyResult=[
                "status"=>false,
                "message"=>"Error #:" . $err
            ];
        }
        else {
            $errpr=$this->errorProcess($result["Status"]);
            if ($errpr["status"]){
                global $twig;
                $message = $twig->render('@placement/successpaymessage.html.twig',
                    [
                        'RefID'=>$result['RefID'],
                    ]);
                $verifyResult=[
                    "status"=>true,
                    "message"=>$message,
                    "refId"=>$result['RefID']
                ];
            }
            else {
                $verifyResult=[
                    "status"=>false,
                    "message"=>$errpr["message"]
                ];
            }
        }
        return $verifyResult;
    }
    private function errorProcess($errorCode){
        switch($errorCode){
            case "-1":
            default:
                $res=[
                    'status'=>false,
                    'message'=>'اطلاعات ارسال شده ناقص است.'
                ];
                break;
            case "-2":
                $res=[
                    'status'=>false,
                    'message'=>'آی پی یا مرچنت کد پذیرنده صحیح نیست.'
                ];
                break;
            case "-3":
                $res=[
                    'status'=>false,
                    'message'=>'با ﺗﻮﺟﻪ ﺑﻪ ﻣﺤﺪﻭﺩﻳﺖ ﻫﺎﻱ ﺷﺎﭘﺮﻙ ﺍﻣﻜﺎﻥ ﭘﺮﺩﺍﺧﺖ ﺑﺎ ﺭﻗﻢ ﺩﺭﺧﻮﺍﺳﺖ ﺷﺪﻩ ﻣﻴﺴﺮ ﻧﻤﻲ ﺑﺎﺷﺪ.'
                ];
                break;
            case "-4":
                $res=[
                    'status'=>false,
                    'message'=>'ﺳﻄﺢ ﺗﺎﻳﻴﺪ ﭘﺬﻳﺮﻧﺪﻩ ﭘﺎﻳﻴﻦ ﺗﺮ ﺍﺯ ﺳﻄﺢ ﻧﻘﺮﻩ ﺍﻱ ﺍﺳﺖ.'
                ];
                break;
            case "-11":
                $res=[
                    'status'=>false,
                    'message'=>'ﺩﺭﺧﻮﺍﺳﺖ ﻣﻮﺭﺩ ﻧﻈﺮ ﻳﺎﻓﺖ ﻧﺸﺪ.'
                ];
                break;
            case "-12":
                $res=[
                    'status'=>false,
                    'message'=>'ﺍﻣﻜﺎﻥ ﻭﻳﺮﺍﻳﺶ ﺩﺭﺧﻮﺍﺳﺖ ﻣﻴﺴﺮ ﻧﻤﻲ ﺑﺎﺷﺪ.'
                ];
                break;
            case "-21":
                $res=[
                    'status'=>false,
                    'message'=>'ﻫﻴﭻ ﻧﻮﻉ ﻋﻤﻠﻴﺎﺕ ﻣﺎﻟﻲ ﺑﺮﺍﻱ ﺍﻳﻦ ﺗﺮﺍﻛﻨﺶ ﻳﺎﻓﺖ ﻧﺸﺪ.'
                ];
                break;
            case "-22":
                $res=[
                    'status'=>false,
                    'message'=>'ﺗﺮﺍﻛﻨﺶ ﻧﺎ ﻣﻮﻓﻖ ﻣﻲﺑﺎﺷﺪ.'
                ];
                break;
            case "-33":
                $res=[
                    'status'=>false,
                    'message'=>'ﺭﻗﻢ ﺗﺮﺍﻛﻨﺶ ﺑﺎ ﺭﻗﻢ ﭘﺮﺩﺍﺧﺖ ﺷﺪﻩ ﻣﻄﺎﺑﻘﺖ ﻧﺪﺍﺭﺩ.'
                ];
                break;
            case "-34":
                $res=[
                    'status'=>false,
                    'message'=>'ﺳﻘﻒ ﺗﻘﺴﻴﻢ ﺗﺮﺍﻛﻨﺶ ﺍﺯ ﻟﺤﺎﻅ ﺗﻌﺪﺍﺩ ﻳﺎ ﺭﻗﻢ ﻋﺒﻮﺭ ﻧﻤﻮﺩﻩ ﺍﺳﺖ.'
                ];
                break;
            case "-40":
                $res=[
                    'status'=>false,
                    'message'=>'ﺍﺟﺎﺯﻩ ﺩﺳﺘﺮﺳﻲ ﺑﻪ ﻣﺘﺪ ﻣﺮﺑﻮﻃﻪ ﻭﺟﻮﺩ ﻧﺪﺍﺭﺩ.'
                ];
                break;
            case "-41":
                $res=[
                    'status'=>false,
                    'message'=>'اطلاعات ارسال شده مربوط به AdditionalData غیر معتبر میباشد.'
                ];
                break;
            case "-42":
                $res=[
                    'status'=>false,
                    'message'=>'مدت زمان معتبر طول عمر شناسه پرداخت باید بین 30 دقیقه تا 45 روز باشد.'
                ];
                break;
            case "-54":
                $res=[
                    'status'=>false,
                    'message'=>'درخواست مورد نظر آرشیو شده است'
                ];
                break;
            case "100":
                $res=[
                    'status'=>true,
                    'message'=>'عملیات با موفقیت انجام گردیده است.'
                ];
                break;
            case "101":
                $res=[
                    'status'=>true,
                    'message'=>'عملیات پرداخت موفق بوده و قبلا تایید پرداخت انجام شده است.'
                ];
                break;
        }
        return $res;
    }
}