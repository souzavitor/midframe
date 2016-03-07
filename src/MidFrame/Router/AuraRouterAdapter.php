<?php
/**
 * Middleware Framework
 *
 * @see         https://github.com/souzavitor/midframe
 * @copyright   Copyright (c) 2015-2016
 * @license     https://github.com/souzavitor/midframe/blob/master/LICENSE.md
 */

namespace MidFrame\Router;

use Psr\Http\Message\ServerRequestInterface as Request;

use Aura\Router\Generator;
use Aura\Router\Router;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;
use Aura\Router\Route;

/**
 * Class to adapt the Aura Router to the application
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class AuraRouterAdapter implements RouterInterface
{
    /**
     * Aura Router
     *
     * @var Aura\Router\Router
     */
    private $router = null;

    /**
     * Route Http allowed methods
     *
     * @var array
     */
    private $httpAllowedMethods = [
        'GET',
        'POST',
        'PUT',
        'HEAD',
        'PATCH',
        'DELETE'
    ];

    /**
     * The application routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * Constructor
     *
     * @param Aura\Router\Router $router
     */
    public function __construct(Router $router = null)
    {
        if (null === $router) {
            $router = $this->createRouter();
        }
        $this->router = $router;
    }

    /**
     * Create a default Aura router instance
     *
     * @return Aura\Router\Router
     */
    private function createRouter()
    {
        return new Router(
            new RouteCollection(new RouteFactory()),
            new Generator()
        );
    }

    /**
     * Add a route in router
     *
     * @param array $spec
     * @return Aura\Router\Route $route
     */
    public function addRoute(array $spec = [])
    {
        if (!isset($spec['path']) || !isset($spec['middleware'])) {
            throw new Exception\InvalidRouteSpec(
                'The route specification MUST have path and middleware'
            );
        }
        $name = isset($spec['name']) ? $spec['name'] : null;
        $path = $spec['path'];
        $middleware = $spec['middleware'];

        $methods = isset($spec['allowed_methods']) ? $spec['allowed_methods'] : [];
        $methods = array_intersect($this->httpAllowedMethods, $methods);
        $options = [];
        if (isset($spec['options'])) {
            $options = $spec['options'];
            if (!is_array($options)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Route options must be an array; received "%s"',
                    gettype($options)
                ));
            }
        }
        $auraRoute = $this->router->add($name, $path, $middleware);

        foreach ($options as $key => $value) {
            switch ($key) {
                case 'tokens':
                    $auraRoute->addTokens($value);
                    break;
                case 'values':
                    $auraRoute->addValues($value);
                    break;
            }
        }

        if (!empty($methods)) {
            $auraRoute->setMethod($methods);
        }

        if (array_key_exists($path, $this->routes)) {
            $methods = array_merge($this->routes[$path], $methods);
        }
        $this->routes[$path] = $methods;
        return $auraRoute;
    }

    /**
     * Match the request path
     *
     * @param Request $request
     * @return Aura\Router\Route $route
     */
    public function match(Request $request)
    {
        $path   = $request->getUri()->getPath();
        $method = $request->getMethod();
        $params = $request->getServerParams();
        $params['REQUEST_METHOD'] = $method;
        $route  = $this->router->match($path, $params);
        if (false === $route) {
            return $this->getFailedRoute($request);
        }
        return RouteResult::fromRouteSuccess($route);
    }

    /**
     * Create and get an object as result of a Failed Route
     *
     * @param Request $request
     * @return FailureRouteResult $route
     */
    public function getFailedRoute(Request $request)
    {
        $failedRoute = $this->router->getFailedRoute();
        if (null === $failedRoute) {
            return RouteResult::fromRouteFailure();
        }
        if ($failedRoute->failedMethod()) {
            return RouteResult::fromRouteFailure($failedRoute->method);
        }
        list($path) = explode('^', $failedRoute->name);
        if (isset($failedRoute->failed)
            && $failedRoute->failed !== Route::FAILED_REGEX
            && array_key_exists($path, $this->routes)
        ) {
            return RouteResult::fromRouteFailure($this->routes[$path]);
        }
        return RouteResult::fromRouteFailure();
    }

    /**
     * Generate a URI from the named route.
     *
     * @see https://github.com/auraphp/Aura.Router#generating-a-route-path
     * @see http://framework.zend.com/manual/current/en/modules/zend.mvc.routing.html
     * @param string $name
     * @param array $substitutions
     * @return string
     * @throws \RuntimeException if unable to generate the given URI.
     */
    public function getUri($name, array $substitutions)
    {

    }
}
