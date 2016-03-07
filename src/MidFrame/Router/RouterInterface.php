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

/**
 * MidFrame Router Interface
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
interface RouterInterface
{
    /**
     * Add a route in router
     *
     * @param array $spec
     * @return Object $route
     */
    public function addRoute(array $spec = []);

    /**
     * Match the request path
     *
     * @param Request $request
     * @return Aura\Router\Route $route
     */
    public function match(Request $request);

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
    public function getUri($name, array $substitutions);
}
