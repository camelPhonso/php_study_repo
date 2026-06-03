<?php

declare(strict_types = 1);

namespace app\Services;

use App\Entity\Invoice;
use App\Enums\InvoiceStatus;
use Doctrine\ORM\EntityManager;

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

    public function insertInvoice(Invoice $invoice) 
    {
        $this->em->persist($invoice);
        $this->em->flush();
    }
}