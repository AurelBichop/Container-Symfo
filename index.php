<?php

use App\Controller\OrderController;
use App\Database\Database;
use App\DependencyInjection\LoggerCompilerPass;
use App\Logger;
use App\Mailer\GmailMailer;
use App\Mailer\SmtpMailer;
use App\Texter\FaxTexter;
use App\Texter\SmsTexter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

require __DIR__ . '/vendor/autoload.php';

$container = new ContainerBuilder();

$container->setParameter('mailer.gmail_user','lior@gmail.com');
$container->setParameter('mailer.gmail_password','1234');


// $controllerDefinition = new Definition(OrderController::class,[
//     new Reference(Database::class),
//     new Reference(GmailMailer::class),
//     new Reference(SmsTexter::class),
// ]);

$container->register('order_controller',OrderController::class)
    ->setPublic(true)
    ->setAutowired(true)
    // ->setArguments([
    //     new Reference(Database::class),
    //     new Reference(GmailMailer::class),
    //     new Reference(SmsTexter::class),
    // ])
    ->addMethodCall('sayHello',[
        'mon Petit Message'
    ])
    ->addMethodCall('setSecondaryMailer',[
        new Reference('mailer.gmail')
    ])
;

$databaseDefinition = new Definition(Database::class);
$container->setDefinition('database',$databaseDefinition);
//$container->set('database',new Database());

// $smsTexterDefinition = new Definition(SmsTexter::class,[
//     "service.sms.com",
//     "apikey123"
// ]);
// $smsTexterDefinition
//     ->addArgument("service.sms.com")
//     ->addArgument("apikey123");

// $smsTexterDefinition->setArguments([
//     "service.sms.com",
//     "apikey123"
// ]);
$container->autowire('texter.sms',SmsTexter::class)
    ->setArguments(["service.sms.com","apikey123"])
    //->addMethodCall('setLogger',[new Reference('logger')])
    ->addTag('with_logger')
    ;


$container->autowire('logger',Logger::class);

// $gmailMailerDefinition = new Definition(GmailMailer::class,[
//     "lior@gmail.com",
//     "123456"
// ]);
// $container->setDefinition('mailer.gmail',$gmailMailerDefinition);
$container->register('mailer.gmail',GmailMailer::class)
    ->setAutowired(true)
    ->setArguments([
        "%mailer.gmail_user%",
        "%mailer.gmail_password%"
    ])
    ->addTag('with_logger')
    //->addMethodCall('setLogger',[new Reference('logger')])
    ;

//var_dump($container->get('mailer.gmail'));

// $database = $container->get('database');
// $texter = $container->get('texter.sms');
// $mailer = $container->get('mailer.gmail');

//$container->setDefinition('order_controller',$controllerDefinition);


$container->autowire('mailer.smtp',SmtpMailer::class)
    ->setArguments(['smtp://localhost','root','123']);

//$container->register('texter.fax',FaxTexter::class);
$container->autowire('texter.fax',FaxTexter::class);

//Ajout d'alias pour les services
$container->setAlias(OrderController::class, 'order_controller')->setPublic(true);
$container->setAlias('App\Database\Database', 'database');

$container->setAlias(GmailMailer::class, 'mailer.gmail');
$container->setAlias(SmtpMailer::class, 'mailer.smtp');
$container->setAlias('App\Mailer\MailerInterface', 'mailer.gmail');

$container->setAlias(SmsTexter::class, 'texter.sms');
$container->setAlias(FaxTexter::class, 'texter.fax');
$container->setAlias('App\Texter\TexterInterface', 'texter.sms');
$container->setAlias('App\Logger', 'logger');


$container->addCompilerPass(new LoggerCompilerPass);

//Evite les références circulaire et optimise la config 
$container->compile();


$controller = $container->get(OrderController::class);

$httpMethod = $_SERVER['REQUEST_METHOD'];

if($httpMethod === 'POST') {
    $controller->placeOrder();
    return;
}

include __DIR__. '/views/form.html.php';
