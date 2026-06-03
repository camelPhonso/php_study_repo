<?php

declare(strict_types= 1);

namespace App\Services;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Enums\InvoiceStatus;

class FileService
{
    public function __construct() {}

    public function receiveUpload()
    {
        if (!isset($_POST)) trigger_error("Something went wrong with your upload.", E_USER_ERROR);

        [
            'name' => $name, 
            'tmp_name' => $tmp_name, 
            'size' => $size, 
            'error' => $error, 
            'type' => $type
        ] = $_FILES["invoice-upload"];

        if ($error !== 0) trigger_error("There was an issue uploading your file.", E_USER_ERROR);

        $name_array = explode(".", $name);
        $extension = strtolower(end($name_array));

        if (!str_contains($extension, "csv")) trigger_error("This file type is not valid.", E_USER_ERROR);

        $new_file_name = uniqid("invoice", true) . "." . $extension;
        $destination = STORAGE_PATH . $new_file_name;

        return move_uploaded_file($tmp_name, $destination) ? $destination : trigger_error("There was an error uploading your file.", E_USER_ERROR);
    }

    public function parseInvoice(string $file_path): Invoice
    {
        $file = fopen($file_path,'r');
        $transactions = [];
        fgetcsv($file); // discard the header row

        while (($row = fgetcsv($file)) !== false) {
            $transactions[] = $row;
        }

        // return array_slice($transactions, -1);
        $invoice = (new Invoice())
            ->setInvoiceNumber($transactions[1][2])
            ->setAmount((float)$this->getTotal($transactions))
            ->setStatus(InvoiceStatus::Pending);

        foreach ($transactions as $transaction) {
            $invoiceItem = (new InvoiceItem())
                ->setDescription($transaction[2])
                ->setQuantity(1)
                ->setUnitPrice((int) $transaction[3])
                ->setInvoice($invoice);
        }

        return $invoice;
    }

    private function getTotal(array $transactions): float
    {
        (float) $total = 0;

        foreach ($transactions as $transaction) {
            $total += floatval($transaction[3]);
        }
        
        return $total;
    }
}