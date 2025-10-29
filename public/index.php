<?php

use App\Kernel;

use App\Controller\BookingController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

require_once __DIR__ . '/../vendor/autoload.php';

$dataDir = __DIR__ . '/../src/Service/data';

// Роутинг
$routes = new RouteCollection();
foreach (get_class_methods(BookingController::class) as $method) {
    $reflection = new ReflectionMethod(BookingController::class, $method);
    $attributes = $reflection->getAttributes(Route::class);
    foreach ($attributes as $attr) {
        $route = $attr->newInstance();
        $routes->add($route->getName(), $route);
    }
}

$context = new RequestContext();
$context->fromRequest(Request::createFromGlobals());
$matcher = new UrlMatcher($routes, $context);

try {
    $request = Request::createFromGlobals();
    $params = $matcher->match($request->getPathInfo());
    $controllerName = $params['_controller'];
    unset($params['_controller'], $params['_route']);

    $controller = new BookingController($dataDir);
    $resolver = new ControllerResolver();
    $argumentResolver = new ArgumentResolver();

    $arguments = $argumentResolver->getArguments($request, [$controller, $controllerName]);
    $response = call_user_func_array([$controller, $controllerName], $arguments);
    $response->send();
} catch (ResourceNotFoundException $e) {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}