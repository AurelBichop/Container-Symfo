<?php

namespace App\Texter;

use App\HasLoggerInterface;
use App\Logger;

class SmsTexter implements TexterInterface,HasLoggerInterface
{
    protected $serviceDsn;
    protected $key;
    protected $logger;

    public function __construct(string $serviceDsn, string $key)
    {
        $this->serviceDsn = $serviceDsn;
        $this->key = $key;
    }

    public function setLogger(Logger $logger){
        $this->logger = $logger; 
        $this->logger->log('Ca fonctionne dans les sms');
    }

    public function send(Text $text)
    {
        var_dump("ENVOI DE SMS : ", $text);
    }
}
