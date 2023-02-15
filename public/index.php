<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use GuzzleHttp\Exception\GuzzleException;
use Task\Controller\Api\Nbp;
use function DI\autowire;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions([
        Nbp::class => autowire(Nbp::class)
    ]);
    $container = $containerBuilder->build();

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode( '/', $uri );
    $uri = array_filter($uri);
    $uri = array_values($uri);

    $requestMethod = $_SERVER["REQUEST_METHOD"];

    $nbp = $container->get(Nbp::class);
    $result = $nbp->process($requestMethod, $uri);

    echo json_encode($result);
} catch (Exception $e) {
    echo $e->getMessage();
    http_response_code(400);
} catch (GuzzleException $e) {
    echo $e->getMessage();
    http_response_code(400);
}
