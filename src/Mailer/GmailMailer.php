<?php

namespace App\Mailer;

use App\HasLoggerInterface;
use App\Logger;

class GmailMailer implements MailerInterface,HasLoggerInterface
{

    protected $user;
    protected $password;
    protected $logger;

    public function __construct(string $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function setLogger(Logger $logger){
        $this->logger = $logger; 
        $this->logger->log('Ca fonctionne dans Gmail');
    }

    public function send(Email $email)
    {
        var_dump("ENVOI VIA GMAILMAILER", $email);
    }
}
