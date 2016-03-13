<?php
namespace MidFrame\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Zend\Diactoros\Response\JsonResponse;

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
        $status = $error instanceof \Exception ? $error->getCode() : 500;
        if (!is_numeric($status)
            || is_float($status)
            || $status < 100
            || $status >= 600
        ) {
            $status = 500;
        }
        $data = [
            'statusCode' => $status,
            'message' => $error->getMessage(),
            'trace' => $error->getTrace()
        ];
        $response = new JsonResponse($data, $status);
        return $response;
    }
}
