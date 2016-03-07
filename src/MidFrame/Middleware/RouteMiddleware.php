<?php
/**
 * Middleware Framework
 *
 * @see         https://github.com/souzavitor/midframe
 * @copyright   Copyright (c) 2015-2016
 * @license     https://github.com/souzavitor/midframe/blob/master/LICENSE.md
 */

namespace MidFrame\Middleware;

use MidFrame\Router\RouterInterface;
use MidFrame\Router\RouteResult;

use Zend\Stratigility\MiddlewarePipe;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The routing middleware
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class RouteMiddleware extends MiddlewarePipe
{

    /**
     * The application router adapter
     * @var RouterInterface The application router interface adapter
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        parent::__construct();
        $this->router = $router;
    }


    /**
     * Overload middleware invocation.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $out
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $result = $this->router->match($request);
        if ($result->isFailure()) {
            if ($result->isMethodFailure()) {
                $response = $response->withStatus(405)
                    ->withHeader('Allow', implode(',', $result->getAllowedMethods()));
                return $next($request, $response, 405);
            }
            return $next($request, $response);
        }

        $request = $request->withAttribute(RouteResult::class, $result);
        foreach ($result->getMatchedParams() as $param => $value) {
            $request = $request->withAttribute($param, $value);
        }
        return $next($request, $response);
    }
}
