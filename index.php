<?php

use App\Controller\OrderController;
use App\DependencyInjection\LoggerCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require __DIR__ . '/vendor/autoload.php';

$start = microtime(true);

if(file_exists(__DIR__."/config/container.php")){
    require_once __DIR__."/config/container.php";
    $container = new ProjectServiceContainer();
}else{
    $container = new ContainerBuilder();

    $loader = new YamlFileLoader($container,new FileLocator([__DIR__.'/config']));
    $loader->load('services.yaml');

    $container->addCompilerPass(new LoggerCompilerPass);

    //Evite les références circulaire et optimise la config 
    $container->compile();

    //Création du fichier de cache
    $dumper = new PhpDumper($container);
    file_put_contents(__DIR__."/config/container.php",$dumper->dump());
}    


$controller = $container->get(OrderController::class);

$duration = microtime(true) - $start;

var_dump("Durée de la construction :", $duration * 1000);

$httpMethod = $_SERVER['REQUEST_METHOD'];

if($httpMethod === 'POST') {
    $controller->placeOrder();
    return;
}

include __DIR__. '/views/form.html.php';
