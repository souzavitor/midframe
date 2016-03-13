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
 * Class to handle with a failure route match
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class FailureRouteResult
{

    /**
     * HTTP response code
     * @var integer
     */
    private $code = null;

    /**
     * HTTP methods
     * @var array
     */
    private $methods = [];

    /**
     * Constructor
     *
     * @param int $code The HTTP error code
     * @param array $code The HTTP methods
     */
    public function __construct($code = null, array $methods = [])
    {
        $this->code = $code;
        $this->methods = $methods;
    }

    /**
     * Get the http response code
     *
     * @return integer $code
     */
    public function getCode()
    {
        return $this->code;
    }
}
