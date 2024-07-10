<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use function preg_match;
use function substr;

/**
 * @extends ServiceEntityRepository<Tax>
 */
class TaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tax::class);
    }

    /**
     * Get Taxes by tax code.
     * @return Tax[] Returns an array of Tax objects
     */
    public function findByTaxes(string $value): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.code LIKE :tax_code')
            ->setParameter('tax_code', '%'.$value.'%')
            ->getQuery()
            ->getResult();
    }

    public function findByTaxPrefix(string $value): ?Tax
    {
        $taxCode = substr($value, 0, 2);
        if (!preg_match('/^[A-Z]{2}+$/', $taxCode, $match)) {
            return null;
        }
        $taxes = $this->findByTaxes(substr($value, 0, 2));
        if (empty($taxes)) {
            return null;
        }
        foreach ($taxes as $tax) {
            $regexCode = $tax->getCode();
            if (preg_match($regexCode, $value, $match)) {
                return $tax;
            }
        }

        return null;
    }
}
