<?php

declare(strict_types = 1);

namespace app\Services;

use App\Entity\Invoice;
use App\Enums\InvoiceStatus;
use Doctrine\ORM\EntityManager;
use App\Types\Result;

class InvoiceService
{
    public function __construct(private EntityManager $em)
    {
    }

    public function getPaidInvoices(): array
    {
        return $this->em->createQueryBuilder()
                        ->select('i')
                        ->from(Invoice::class, 'i')
                        ->where('i.status = :status')
                        ->setParameter('status', InvoiceStatus::Paid)
                        ->getQuery()
                        ->getArrayResult();
    }

    public function insertInvoice(Invoice $invoice): Result
    {
        try {
            $this->em->beginTransaction();
    
            $this->em->persist($invoice);
    
            foreach( $invoice->getItems() as $item) {
                $this->em->persist($item);
            }
    
            $result = $this->em->flush()->getArrayResult();

            return Result::Ok($result);
        } catch (\Throwable $e) {
            return Result::Fail($e);
        }
    }
}