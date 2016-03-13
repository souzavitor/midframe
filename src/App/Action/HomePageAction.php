<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Zend\Diactoros\Response\JsonResponse;

use MidFrame\Middleware\ActionMiddleware;

/**
 * A welcome page
 *
 * Here we can tel more about our framework (but only if you want to share your ideas)
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class HomePageAction extends ActionMiddleware
{
    /**
     * @see \Zend\Stratigility\MiddlewareInterface
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        return new JsonResponse([
            'welcome' => 'Welcome to MidFrame!',
            'description' => 'MidFrame is a new framework to create simple and light middleware applications. MidFrame can create APIs with diffrente resource formats in a easy and fast way.',
            'inspire' => 'Have fun...',
            'status' => $response->getStatusCode(),
            'trace' => debug_backtrace()
        ]);
    }
}
