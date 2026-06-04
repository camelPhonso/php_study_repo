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
            $transactions[] = ['date' => $row[0], 'checkNumber' => $row[1], 'description' => $row[2], 'amount' => $row[3]];
        }

        $invoice = (new Invoice())
            ->setInvoiceNumber($transactions[0]['checkNumber'])
            ->setAmount((float)$this->getTotal($transactions))
            ->setStatus(InvoiceStatus::Pending);

        foreach ($transactions as $transaction) {
            $invoice->addItem(
                (new InvoiceItem())
                    ->setDescription($transaction['description'])
                    ->setQuantity(1)
                    ->setUnitPrice($this->parseTotal($transaction['amount']))
                    ->setInvoice($invoice)
            );
        }

        return $invoice;
    }

    private function getTotal(array $transactions): float
    {
        return array_sum(array_map(fn($array)=>$this->parseTotal($array['amount']), $transactions)); 
    }

    private function parseTotal(string $amount): int
    {
        $newString = str_replace('$','', $amount);
        return intval($newString);
    }
}