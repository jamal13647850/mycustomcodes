<?php
/**
 * @author Sayyed Jamal Ghasemi <https://www.linkedin.com/in/jamal1364/>
 * Date: 12/14/2016
 * Time: 10:11 PM
 */

namespace jamal\mycustomcodes;
;


class PGBot{
    private $SLACK_WEBHOOK;
    private $telegramBotUrl;
    public function __construct(){
        $this->SLACK_WEBHOOK='';
        $this->telegramBotUrl='';
    }
    public function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    public function __get($name) {
        return $this->vars[$name];
    }
    public function __call($name, $arguments){
    }
    public function sendTelegramBot($newurl){
        ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->telegramBotUrl.$newurl );  // Pass URL as parameter.
        $result = curl_exec($ch);  // grab URL and pass it to the variable.
        curl_close($ch);  // close curl resource, and free up system resources.
        $output = ob_get_contents();
        ob_end_clean();

    }
    public function sendSlack($text){
        ob_start();
        $message = array('payload' => json_encode(array('text' => $text)));
        // Use curl to send your message
        $c = curl_init($this->SLACK_WEBHOOK);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $message);
        curl_exec($c);
        curl_close($c);
        $output = ob_get_contents();
        ob_end_clean();


    }
    
    /**
     * @param string $SLACK_WEBHOOK
     * @return PGBot
     */
    public function setSLACKWEBHOOK($SLACK_WEBHOOK)
    {
        $this->SLACK_WEBHOOK = $SLACK_WEBHOOK;
        return $this;
    }

    /**
     * @param string $telegramBotUrl
     * @return PGBot
     */
    public function setTelegramBotUrl($telegramBotUrl)
    {
        $this->telegramBotUrl = $telegramBotUrl;
        return $this;
    }
}