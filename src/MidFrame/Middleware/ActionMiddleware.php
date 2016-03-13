<?php
namespace MidFrame\Middleware;

use Zend\Stratigility\MiddlewareInterface;

use Zend\ServiceManager\ServiceManager;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Abstract class to create an action middleware
 *
 * Here we find some attributes and methods with usefull objects of our application.
 *
 * @author Vitor de Souza <souza.vitor@outlook.com.br>
 */
abstract class ActionMiddleware implements MiddlewareInterface
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
     * The matched parameters in route
     * @var array
     */
    protected $matchedParams = [];

    /**
     * Action middleware construct
     *
     * @param array $config
     * @param ServiceManager $container
     */
    public function __construct(array $config = [], ServiceManager $container = null)
    {
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * Process an incoming request and/or response.
     *
     * @see \Zend\Stratigility\MiddlewareInterface
     * @param Request $request
     * @param Response $response
     * @param null|callable $out
     * @return null|Response
     */
    abstract public function __invoke(Request $request, Response $response, callable $out = null);

    /**
     * Sets the matched parameters if any
     *
     * @param array $matchedParams
     */
    public function setMatchedParams($matchedParams)
    {
        $this->matchedParams = $matchedParams;
    }
}
