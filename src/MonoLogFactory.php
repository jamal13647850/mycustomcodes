<?php
namespace jamal13647850\mycustomcodes;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class MonoLogFactory{
    private $log;
    public function __construct($logChanel,$logPath=__DIR__."/logs.log",$level=Logger::DEBUG)
    {
        // create a log channel
        $this->log = new Logger($logChanel);
        $this->log->pushHandler(new StreamHandler($logPath, $level));
       
    }
    public function getLogger(){
        return $this->log;
    }
}