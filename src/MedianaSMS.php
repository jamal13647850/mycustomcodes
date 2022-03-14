<?php

declare(strict_types=1);

namespace jamal\mycustomcodes;

defined('ABSPATH') || exit();


class MedianaSMS
{
    private string $url;
    private string $uname;
    private string $pass;
    private string $from;

    public function __construct( string $uname, string $pass, string $from,string $url = 'https://ippanel.com/services.jspd')
    {
        $this->url = $url;
        $this->uname = $uname;
        $this->pass = $pass;
        $this->from = $from;
    }
    public function sendSMS(array $rcpt_nm,$message):array
    {

        $param = [
            'uname' => $this->uname,
            'pass' => $this->pass,
            'from' => $this->from,
            'message' => $message,
            'to' => json_encode($rcpt_nm),
            'op' => 'send'
        ];

        $handler = curl_init($this->url);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_POSTFIELDS, $param);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response2 = curl_exec($handler);

        $response2 = json_decode($response2);
        $res_code = $response2[0];
        $res_data = $response2[1];


       
        return [$res_code,$res_data];
    }
}
