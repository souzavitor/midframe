<?php
namespace MidFrame\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Zend\Stratigility\ErrorMiddlewareInterface;

/**
 * Erro handler Middleware
 *
 * Middleware to handle the application errors
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class ErrorHandlerMiddleware implements ErrorMiddlewareInterface
{
    /**
     * Invoke the error handler middleware
     *
     * @param mixed $error
     * @param Request $request
     * @param Response $response
     * @param callable $out
     * @return Response
     */
    public function __invoke($error, Request $request, Response $response, callable $out = null)
    {
        $result = $out($request, $response, $error);
        return ($result instanceof Response ? $result : $response);
    }
}
