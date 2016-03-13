<?php
/**
 * Middleware Framework
 *
 * @see         https://github.com/souzavitor/midframe
 * @copyright   Copyright (c) 2015-2016
 * @license     https://github.com/souzavitor/midframe/blob/master/LICENSE.md
 */

namespace MidFrame\Middleware;

use Zend\Stratigility\MiddlewarePipe;
use Zend\ServiceManager\ServiceManager;

use MidFrame\Router\RouteResult;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use ReflectionClass;

/**
 * Dispath the middleware matched by route
 */
class DispatchMiddleware extends MiddlewarePipe
{

    /**
     * The application configuration
     * @var array
     */
    protected $config = [];

    /**
     * The application container
     * @var \Zend\ServiceManager\ServiceManager;
     */
    protected $container = null;

    /**
     * Action middleware construct
     * @param array $config
     * @param ServiceManager $container
     */
    public function __construct(ServiceManager $container = null)
    {
        $config = $container->has('AppConfig') ? $container->get('AppConfig')->get() : [];
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * Invoke the middleware with the matched route
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $out
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        $routeResult = $request->getAttribute(RouteResult::class, false);
        if (!$routeResult) {
            return $next($request, $response);
        }
        $matchedMiddleware = $routeResult->getMatchedMiddleware();
        if (is_string($matchedMiddleware)) {
            $ref = new ReflectionClass($matchedMiddleware);
            if (!$ref->isSubclassOf(ActionMiddleware::class)) {
                throw new Exception\InvalidMiddlewareException(
                    sprintf('The Action in router must be an instance of %s.', ActionMiddleware::class)
                );
            }
            $matchedParams = $routeResult->getMatchedParams();
            $constructor = $ref->getConstructor();
            $parameters = $constructor->getParameters();
            $middleware = $ref->newInstance((array) $this->config, $this->container);
            $middleware->setMatchedParams($matchedParams);
        } else {
            throw new Exception\InvalidMiddlewareException(
                'The Action Middleware must be passed as string in router configuration.'
            );
        }
        return $middleware($request, $response);
    }
}
