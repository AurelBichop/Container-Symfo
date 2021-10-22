<?php

use App\Controller\OrderController;
use App\DependencyInjection\LoggerCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


require __DIR__ . '/vendor/autoload.php';

$container = new ContainerBuilder();


$loader = new YamlFileLoader($container,new FileLocator([__DIR__.'/config']));
$loader->load('services.yaml');


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
