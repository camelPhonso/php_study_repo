<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Services\FileService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(private readonly Twig $twig, private readonly FileService $fileService)
    {
    }

    public function index(Request $request, Response $response, $args): Response
    {
        return $this->twig->render($response, 'index.twig');
    }
}
