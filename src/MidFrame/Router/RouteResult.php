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
     * @var int code
     */
    private $code;

    /**
     * @var string
     */
    private $failed = null;

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
    public static function fromRouteFailure($failed)
    {
        $result = new self();
        $result->success = false;
        $result->failed = $failed;
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
     *
     * Check whether a failure happened due to accept header
     *
     * @return bool
     */
    public function failedAccept()
    {
        return $this->failed == \Aura\Router\Route::FAILED_ACCEPT;
    }

    /**
     *
     * Check whether a failure happened due to http method
     *
     * @return bool
     */
    public function failedMethod()
    {
        return $this->failed == \Aura\Router\Route::FAILED_METHOD;
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
     * Sets the allowed methods for the route failure.
     *
     * @return string[] HTTP methods allowed
     */
    public function setAllowedMethods(array $methods)
    {
        if ($this->isSuccess()) {
            $this->allowedMethods = [];
        }
        $this->allowedMethods = $methods;
    }

    /**
     * Sets the allowed methods for the route failure.
     *
     * @return string[] HTTP methods allowed
     */
    public function setAccept(array $accept)
    {
        if ($this->isSuccess()) {
            $this->accept = [];
        }
        $this->accept = $accept;
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
     * Gets the http error code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the http error code
     *
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Only allow instantiation via factory methods.
     */
    private function __construct()
    {
    }
}
