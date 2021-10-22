<?php

use App\Controller\OrderController;
use App\Database\Database;
use App\Logger;
use App\Mailer\GmailMailer;
use App\Mailer\SmtpMailer;
use App\Texter\FaxTexter;
use App\Texter\SmsTexter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function(ContainerConfigurator $configurator){
    $parameters = $configurator->parameters();

    $parameters
        ->set('mailer.gmail_user','lior@gmail.com')
        ->set('mailer.gmail_password','1234')
        ;

    $services = $configurator->services();   

    $services->defaults()
        ->autowire(true);

    $services
        ->set('order_controller',OrderController::class)
        ->public()
        ->call('sayHello',['Bonjour à tous',33])
        ->call('setSecondaryMailer',[service('mailer.gmail')])
        
        
        ->set('database',Database::class)
        

        ->set('logger',Logger::class)
        

        ->set('texter.sms', SmsTexter::class)
        ->args(['services.sms.com','apikey1234'])
        ->tag('with_logger')

        ->set('mailer.gmail', GmailMailer::class)
        
        ->args(['%mailer.gmail_user%','%mailer.gmail_password%'])
        ->tag('with_logger')

        ->set('mailer.smtp', SmtpMailer::class)
        ->args(['smtp://localhost','root','123'])
        
        ->set('texter.fax', FaxTexter::class)
        

        //Ajout d'alias pour les services
        ->alias(OrderController::class, 'order_controller')->public()
        ->alias('App\Database\Database', 'database')

        ->alias(GmailMailer::class, 'mailer.gmail')
        ->alias(SmtpMailer::class, 'mailer.smtp')
        ->alias('App\Mailer\MailerInterface', 'mailer.gmail')

        ->alias(SmsTexter::class, 'texter.sms')
        ->alias(FaxTexter::class, 'texter.fax')
        ->alias('App\Texter\TexterInterface', 'texter.sms')
        ->alias('App\Logger', 'logger')
        ;
};