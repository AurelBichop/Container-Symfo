<?php

use App\Controller\OrderController;
use App\Database\Database;
use App\Mailer\GmailMailer;
use App\Texter\SmsTexter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

require __DIR__ . '/vendor/autoload.php';

$container = new ContainerBuilder();

$container->setParameter('mailer.gmail_user','lior@gmail.com');
$container->setParameter('mailer.gmail_password','1234');


$controllerDefinition = new Definition(OrderController::class,[
    new Reference(Database::class),
    new Reference(GmailMailer::class),
    new Reference(SmsTexter::class),
]);

$controllerDefinition
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

$smsTexterDefinition = new Definition(SmsTexter::class,[
    "service.sms.com",
    "apikey123"
]);
// $smsTexterDefinition
//     ->addArgument("service.sms.com")
//     ->addArgument("apikey123");

// $smsTexterDefinition->setArguments([
//     "service.sms.com",
//     "apikey123"
// ]);
$container->setDefinition('texter.sms',$smsTexterDefinition);


// $gmailMailerDefinition = new Definition(GmailMailer::class,[
//     "lior@gmail.com",
//     "123456"
// ]);
// $container->setDefinition('mailer.gmail',$gmailMailerDefinition);
$container->register('mailer.gmail',GmailMailer::class)
    ->setArguments([
        "%mailer.gmail_user%",
        "%mailer.gmail_password%"
    ]);

//var_dump($container->get('mailer.gmail'));

// $database = $container->get('database');
// $texter = $container->get('texter.sms');
// $mailer = $container->get('mailer.gmail');
$container->setDefinition('order_controller',$controllerDefinition);

//Ajout d'alias pour les services
$container->setAlias(OrderController::class, 'order_controller');
$container->setAlias('App\Database\Database', 'database');
$container->setAlias(GmailMailer::class, 'mailer.gmail');
$container->setAlias(SmsTexter::class, 'texter.sms');

$controller = $container->get(OrderController::class);


$httpMethod = $_SERVER['REQUEST_METHOD'];

if($httpMethod === 'POST') {
    $controller->placeOrder();
    return;
}

include __DIR__. '/views/form.html.php';
