<?php
namespace HcfStoreProductCategory\Service;

use HcbStoreProduct\Entity\Product as ProductEntity;
use HcbStoreProductCategory\Entity\Category as CategoryEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class FetchCategoryByProductService
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
     * @return CategoryEntity | null
     */
    public function fetch(ProductEntity $productEntity)
    {
        /* @var $qb QueryBuilder */
        $qb = $this->entityManager
                   ->getRepository('HcbStoreProductCategory\Entity\Category')
                   ->createQueryBuilder('c');

        $qb = $qb->select(array('c'))
           ->join('c.product', 'p')
           ->where('c.enabled = 1')
           ->andWhere('p = :product');

        $qb->setParameter('product', $productEntity);

        return $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
