<?php
/**
 * Middleware Framework
 *
 * @see         https://github.com/souzavitor/midframe
 * @copyright   Copyright (c) 2015-2016
 * @license     https://github.com/souzavitor/midframe/blob/master/LICENSE.md
 */

namespace MidFrame\Container;

use SplPriorityQueue;

use MidFrame\Application;
use MidFrame\Router\RouterInterface;
use MidFrame\Router\AuraRouterAdapter;
use MidFrame\Exception\InvalidArgumentException;

use Interop\Container\ContainerInterface;

use Zend\Diactoros\Response\EmitterInterface;

use Aura\Router\Generator;
use Aura\Router\Route as AuraRoute;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;
use Aura\Router\Router;
use Aura\Router\Regex;

/**
 * Factory to create the application
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class ApplicationFactory
{
    /**
     * Create and return an Application instance.
     *
     * See the class level docblock for information on what services this
     * factory will optionally consume.
     *
     * @param ContainerInterface $container
     * @return Application
     */
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->has('MidFrame\Router\RouterInterface')
            ? $container->get('MidFrame\Router\RouterInterface')
            : new AuraRouterAdapter;

        $finalHandler = $container->has('MidFrame\FinalHandler')
            ? $container->get('MidFrame\FinalHandler')
            : null;

        $emitter = $container->has(EmitterInterface::class)
            ? $container->get(EmitterInterface::class)
            : null;

        $errorMiddleware = $container->has('MidFrame\ErrorHandlerMiddleware')
            ? $container->get('MidFrame\ErrorHandlerMiddleware')
            : null;

        $app = new Application($router, $container, $finalHandler, $emitter);

        $this->injectRoutes($app, $container);
        $app->pipeErrorMiddleware($errorMiddleware);
        return $app;
    }

    /**
     * Inject routes from configuration, if any.
     *
     * @param Application $app
     * @param ContainerInterface $container
     */
    private function injectRoutes(Application $app, ContainerInterface $container)
    {
        $config = $container->has('AppConfig') ? $container->get('AppConfig')->get() : [];
        if (isset($config['routes']) && is_array($config['routes'])) {
            $routes = $config['routes'];
            foreach ($routes as $spec) {
                if (!isset($spec['path']) || !isset($spec['middleware'])) {
                    continue;
                }
                $app->route($spec);
            }
        }
        $app->pipeRouteMiddleware();
    }
}
