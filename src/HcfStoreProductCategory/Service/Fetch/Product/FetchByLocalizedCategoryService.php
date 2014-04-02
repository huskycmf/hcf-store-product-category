<?php
namespace HcfStoreProductCategory\Service\Fetch\Product;

use HcbStoreProductCategory\Entity\Category\Localized as LocalizedEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class FetchByLocalizedCategoryService
{
   /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param LocalizedEntity $localizedEntity
     * @return \HcbStoreProduct\Entity\Product\Localized[]
     */
    public function fetch(LocalizedEntity $localizedEntity)
    {
        /* @var $qb QueryBuilder */
        $qb = $this->entityManager
                   ->getRepository('HcbStoreProduct\Entity\Product\Localized')
                   ->createQueryBuilder('pl');

        /* @var $pqb QueryBuilder */
        $pqb = $this->entityManager
                    ->getRepository('HcbStoreProductCategory\Entity\Category')
                    ->createQueryBuilder('c');

        $pqb->select(array('p.id'))
            ->join('c.product', 'p')
            ->join('c.localized', 'cl')
            ->where('p.enabled = 1')
            ->andWhere('cl = :localized');

        $qb->select(array('pl'))
           ->where('pl.product IN ('.$pqb->getDQL().')')
           ->setParameter('localized', $localizedEntity);

        return $qb->getQuery()->getResult();
    }
}
