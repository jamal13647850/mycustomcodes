<?php
declare (strict_types = 1);
namespace jamal\mycustomcodes;

class MailTrap{ 
    private  $Username, $Password, $Host, $SMTPAuth  ,$port ;
    public function __construct(string $Username,string $Password,string $Host="mailtrap.io",bool $SMTPAuth = true ,int $port = 2525){
        $this->Username = $Username;
        $this->Password = $Password;
        $this->Host     = $Host;
        $this->SMTPAuth = $SMTPAuth;
        $this->port     = $port;
    }
    public function setPhpMailerTrap(){
        add_action('phpmailer_init', [$this,'mailtrap']);
    }
    public function mailtrap($phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host     = $this->Host;
        $phpmailer->SMTPAuth = $this->SMTPAuth;
        $phpmailer->Port     = $this->port ;
        $phpmailer->Username = $this->Username;
        $phpmailer->Password = $this->Password;
    }
   
}