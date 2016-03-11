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
            $args = $routeResult->getMatchedParams();
            if ($ref->getConstructor()) {
                $middleware = $ref->newInstance($args);
            } else {
                $middleware = $ref->newInstance();
            }
        } else {
            throw new InvalidMiddlewareException();
        }
        return $middleware($request, $response);
    }
}
