<?php
namespace HcfStoreProductCategory\Service;

use HcbStoreProduct\Entity\Product as ProductEntity;
use HcbStoreProductCategory\Entity\Category\Localized as LocalizedCategoryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class FetchLocalizedCategoryByProductService
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
     * @param ProductEntity $productEntity
     * @return LocalizedCategoryEntity | null
     */
    public function fetch(ProductEntity $productEntity)
    {
        /* @var $qb QueryBuilder */
        $qb = $this->entityManager
                   ->getRepository('HcbStoreProductCategory\Entity\Category\Localized')
                   ->createQueryBuilder('l');

        $qb = $qb->select(array('l'))
           ->join('l.category', 'c')
           ->join('l.locale', 'loc')
           ->join('c.product', 'p')
           ->where('c.enabled = 1')
           ->andWhere('p = :product');

        $qb->setParameter('product', $productEntity);

        return $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
