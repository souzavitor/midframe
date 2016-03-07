<?php
/**
 * Middleware Framework
 *
 * @see         https://github.com/souzavitor/midframe
 * @copyright   Copyright (c) 2015-2016
 * @license     https://github.com/souzavitor/midframe/blob/master/LICENSE.md
 */

namespace MidFrame;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Application configuration
 *
 * This class is used to manage and get the configurations in the application.
 *
 * @property array $config The array with all the configurations loaded in the application
 * @property string $configBasePath The location where the configurations are stored
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class AppConfig
{
    /**
     * Configuration variables
     * @var array
     */
    private $config = [];

    /**
     * The configurations base path
     * @var string
     */
    private $configBasePath = 'config/';

    public function __construct()
    {
        $this->config = $this->get();
    }

    /**
     * Get app config
     * @param string $key
     * @param string $file
     * @return array
     */
    public function get($key = null)
    {
        if (empty($this->config)) {
            $this->config = include $this->configBasePath . 'config.php';
        }
        if (is_null($key)) {
            return $this->config;
        } elseif (isset($this->config[$key])) {
            return $this->config[$key];
        } else {
            return false;
        }
    }
}
