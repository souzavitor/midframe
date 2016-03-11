<?php
/**
 * Middleware Framework
 *
 * @see         https://github.com/souzavitor/midframe
 * @copyright   Copyright (c) 2015-2016
 * @license     https://github.com/souzavitor/midframe/blob/master/LICENSE.md
 */

namespace MidFrame;

use MidFrame\Router\RouterInterface;
use MidFrame\Router\AuraRouterAdapter as Router;
use MidFrame\Router\FailureRouteResult;
use MidFrame\Middleware\RouteMiddleware;
use MidFrame\Middleware\DispatchMiddleware;
use MidFrame\Middleware\ErrorHandlerMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\EmitterInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

use Zend\Stratigility\MiddlewarePipe;
use Zend\Stratigility\ErrorMiddlewareInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * The Middleware Application Class
 *
 * This the main class of the application. Here we have all we need to pipe the middlewares.
 * The Application uses the Zend\Stratigility\MiddlewarePipe to create a middleware pipe with PSR-7 concept.
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class Application extends MiddlewarePipe
{
    /**
     * @var AuraRouterAdapter
     */
    private $router;

    /**
     * @var ServiceManager
     */
    private $container;

    /**
     * @var EmitterInterface
     */
    private $emitter;

    /**
     * @var callable
     */
    private $finalHandler;

    /**
     * @var string[]
     */
    private $httpRouteMethods = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    /**
     * Constructor
     *
     * Calls on the parent constructor, and then uses the provided arguments
     * to set internal properties.
     *
     * @param Router\FailureRouteResult $router
     * @param null|ServiceManager $container Zend\ServiceManager to use as Container
     * @param null|callable $finalHandler Final handler to use when $out is not
     *     provided on invocation.
     * @param null|EmitterInterface $emitter Emitter to use when `run()` is
     *     invoked.
     */
    public function __construct(
        RouterInterface $router,
        ServiceManager $container = null,
        callable $finalHandler = null,
        EmmiterInterface $emitter = null
    ) {
        parent::__construct();
        $this->router       = $router;
        $this->container    = $container;
        $this->finalHandler = $finalHandler;
        $this->emitter      = $emitter;
    }

    /**
     * Overload middleware invocation.
     *
     * If $out is not provided, uses the result of `getFinalHandler()`.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $out
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $out = null)
    {
        $out = $out ?: $this->getFinalHandler($response);
        return parent::__invoke($request, $response, $out);
    }

    /**
     * Runs the application with the request and response
     *
     * @param null|ServerRequestInterface $request
     * @param null|ResponseInterface $response
     */
    public function run(ServerRequestInterface $request = null, ResponseInterface $response = null)
    {
        $request  = $request ?: ServerRequestFactory::fromGlobals();
        $response = $response ?: new Response();
        $response = $this($request, $response);
        $emitter = $this->getEmitter();
        $emitter->emit($response);
    }

    /**
     * Retrieve an emitter to use during run().
     *
     * If none was registered during instantiation, this will lazy-load an
     * EmitterStack composing an SapiEmitter instance.
     *
     * @return EmitterInterface
     */
    public function getEmitter()
    {
        if (! $this->emitter) {
            $this->emitter = new SapiEmitter();
        }
        return $this->emitter;
    }

    /**
     * Return the final handler to use during `run()` if the stack is exhausted.
     *
     * @param null|ResponseInterface $response Response instance with which to seed the
     *     FinalHandler; used to determine if the response passed to the handler
     *     represents the original or final response state.
     * @return callable|null
     */
    public function getFinalHandler(ResponseInterface $response = null)
    {
        if (!$this->finalHandler) {
            return null;
        }
        if (method_exists($this->finalHandler, 'setOriginalResponse')) {
            $this->finalHandler->setOriginalResponse($response);
        }
        return $this->finalHandler;
    }

    /**
     * Add a route in the Router
     *
     * @param array $spec
     * @return void
     */
    public function route(array $spec = [])
    {
        $this->router->addRoute($spec);
    }

    /**
     * Pipe the router middleware and the dispatch middleware
     *
     * @return void
     */
    public function pipeRouteMiddleware()
    {
        $this->pipe(new RouteMiddleware($this->router));
        $this->pipe(new DispatchMiddleware);
    }

    /**
     * Pipe the error handler
     *
     *
     * @return void
     */
    public function pipeErrorMiddleware(ErrorMiddlewareInterface $errorMiddleware = null)
    {
        if (is_null($errorMiddleware)) {
            $errorMiddleware = new ErrorHandlerMiddleware;
        }
        $this->pipe($errorMiddleware);
    }
}
