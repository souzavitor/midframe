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

class HomePageAction
{
    private $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        return new JsonResponse([
            'welcome' => $this->message
        ]);
    }
}
