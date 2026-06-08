<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Services\InvoiceService;
use App\Services\FileService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class InvoiceController
{
    public function __construct(private readonly Twig $twig, private readonly InvoiceService $invoiceService, private readonly FileService $fileService)
    {
    }

    public function index(Request $request, Response $response, $args): Response
    {
        return $this->twig->render(
            $response,
            'invoices/index.twig',
            ['invoices' => $this->invoiceService->getPaidInvoices()]
        );
    }

    public function upload(Request $request, Response $response, $args): Response
    {
        return $this->twig->render($response, 'upload/index.twig');
    }

    public function processFile(Request $request, Response $response, $args): Response
    {
        $file_path = $this->fileService->receiveUpload();
        $invoice = $this->fileService->parseInvoice($file_path);

        $result = $this->invoiceService->insertInvoice($invoice);

        return $this->twig->render($response, $result->IsSuccess() ? "upload/outcome.twig" : "error/generic.twig");
    }
}
