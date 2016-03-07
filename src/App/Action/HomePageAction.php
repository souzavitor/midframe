<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Expressive\Plates\PlatesRenderer;
use Zend\Expressive\Twig\TwigRenderer;
use Zend\Expressive\ZendView\ZendViewRenderer;

/**
 * A welcome page
 *
 * Here we can tel more about our framework (but only if you want to share your ideas)
 *
 * @author Vitor de Souza <souza.vitor@outlook.com>
 */
class HomePageAction
{
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        return new JsonResponse([
            'welcome' => 'Welcome to MidFrame!',
            'description' => 'MidFrame is a new framework to create simple and light middleware applications. MidFrame can create APIs with diffrente resource formats in a easy and fast way.',
            'inspire' => 'Have fun...'
        ]);
    }
}
