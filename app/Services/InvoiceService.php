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
    
            $this->em->flush();
            $this->em->commit();

            return Result::Ok($invoice);
        } catch (\Throwable $e) {
            $this->em->rollback();
            $this->em->clear();

            return Result::Fail($e);
        }
    }
}