<?php
/**
 * Middleware Framework
 *
 * @see         https://github.com/souzavitor/midframe
 * @copyright   Copyright (c) 2015-2016
 * @license     https://github.com/souzavitor/midframe/blob/master/LICENSE.md
 */

namespace MidFrame\Router;

/**
 * Class to handle the route result
 *
 * The result can be a failure or a success
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class RouteResult
{
    /**
     * @var null|array
     */
    private $allowedMethods;
    /**
     * @var array
     */
    private $matchedParams = [];

    /**
     * @var string
     */
    private $matchedRouteName;

    /**
     * @var callable|string
     */
    private $matchedMiddleware;

    /**
     * @var bool Success state of routing.
     */
    private $success;

    /**
     * @var Aura Route
     */
    private $route;

    /**
     * Create an instance repesenting a route success match.
     *
     * @param Aura\Router\Route $route
     * @return new self()
     */
    public static function fromRouteSuccess($route)
    {
        $result                    = new self();
        $result->success           = true;
        $result->matchedRouteName  = $route->name;
        $result->matchedMiddleware = $route->params['action'];
        $result->matchedParams     = $route->params;
        $result->allowedMethods    = $route->method;
        $result->route             = $route;

        unset($result->matchedParams['action']);
        return $result;
    }

    /**
     * Create an instance representing a route failure.
     *
     * @param null|int|array $methods HTTP methods allowed for the current URI, if any
     * @return static
     */
    public static function fromRouteFailure($methods = null)
    {
        $result = new self();
        $result->success = false;
        if ($methods === null) {
            $result->allowedMethods = ['*'];
        }
        if (is_array($methods)) {
            $result->allowedMethods = $methods;
        }
        return $result;
    }

    /**
     * Return whether or not the route is successful matched
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Retrieve the matched route name, if possible.
     *
     * @return false|string
     */
    public function getMatchedRouteName()
    {
        if ($this->isFailure()) {
            return false;
        }
        return $this->matchedRouteName;
    }

    /**
     * Retrieve the matched middleware.
     *
     * @return false|callable|string|array
     */
    public function getMatchedMiddleware()
    {
        if ($this->isFailure()) {
            return false;
        }
        return $this->matchedMiddleware;
    }

    /**
     * Returns the matched params.
     *
     * @return array
     */
    public function getMatchedParams()
    {
        return $this->matchedParams;
    }

    /**
     * * Return whether or not the route is failure matched
     *
     * @return bool
     */
    public function isFailure()
    {
        return (!$this->success);
    }

    /**
     * Does the result represent failure to route due to HTTP method?
     *
     * @return bool
     */
    public function isMethodFailure()
    {
        if ($this->isSuccess() || null === $this->allowedMethods) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve the allowed methods for the route failure.
     *
     * @return string[] HTTP methods allowed
     */
    public function getAllowedMethods()
    {
        if ($this->isSuccess()) {
            return [];
        }
        if (null === $this->allowedMethods) {
            return [];
        }
        return $this->allowedMethods;
    }

    /**
     * Get the Aura Route matched
     *
     * @return Aura\Router\Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Only allow instantiation via factory methods.
     */
    private function __construct()
    {
    }
}
