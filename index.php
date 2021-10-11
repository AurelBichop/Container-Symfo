<?php

use App\Controller\OrderController;
use App\Database\Database;
use App\Mailer\GmailMailer;
use App\Texter\SmsTexter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

require __DIR__ . '/vendor/autoload.php';

$container = new ContainerBuilder();


$controllerDefinition = new Definition(OrderController::class,[
    new Reference('database'),
    new Reference('mailer.gmail'),
    new Reference('texter.sms'),
]);



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

$gmailMailerDefinition = new Definition(GmailMailer::class,[
    "lior@gmail.com",
    "123456"
]);
$container->setDefinition('mailer.gmail',$gmailMailerDefinition);

// $database = $container->get('database');
// $texter = $container->get('texter.sms');
// $mailer = $container->get('mailer.gmail');


$container->setDefinition('order_controller',$controllerDefinition);

$controller = $container->get('order_controller');


$httpMethod = $_SERVER['REQUEST_METHOD'];

if($httpMethod === 'POST') {
    $controller->placeOrder();
    return;
}

include __DIR__. '/views/form.html.php';
