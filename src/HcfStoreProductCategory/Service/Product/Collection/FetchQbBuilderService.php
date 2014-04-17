<?php
namespace HcfStoreProductCategory\Service\Product\Collection;

use HcBackend\Service\Fetch\Paginator\QueryBuilder\ResourceDataServiceInterface;
use HcCore\Entity\EntityInterface;
use HcCore\Service\Filtration\Query\FiltrationServiceInterface;
use HcCore\Service\Sorting\SortingServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use HcfStoreProductCategory\Exception\InvalidArgumentException;
use Zend\Stdlib\Parameters;

class FetchQbBuilderService implements ResourceDataServiceInterface
{
    /**
     * @var SortingServiceInterface
     */
    protected $sortingService;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var FiltrationServiceInterface
     */
    protected $filtrationService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SortingServiceInterface $sortingService
     * @param FiltrationServiceInterface $filtrationService
     */
    public function __construct(EntityManagerInterface $entityManager,
                                SortingServiceInterface $sortingService,
                                FiltrationServiceInterface $filtrationService)
    {
        $this->entityManager = $entityManager;
        $this->sortingService = $sortingService;
        $this->filtrationService = $filtrationService;
    }

    /**
     * @param EntityInterface|mixed $localizedCategoryResource
     * @param Parameters $params
     * @return QueryBuilder
     * @throws \HcfStoreProductCategory\Exception\InvalidArgumentException
     */
    public function fetch($categoryResource, Parameters $params = null)
    {
        $localizedCategoryId = $categoryResource;

        if ($categoryResource instanceof \HcbStoreProductCategory\Entity\Category) {
            $localizedCategoryId = $categoryResource->getId();
        } else if (!is_numeric($categoryResource)) {
            throw new InvalidArgumentException("Invalid resource type for localizedCategoryResource");
        }

        /* @var $qb QueryBuilder */
        $qb = $this->entityManager
                   ->getRepository('HcbStoreProduct\Entity\Product')
                   ->createQueryBuilder('p');

        $qb->select(array('p'))
            ->where('p IN ('.$this->entityManager
                    ->getRepository('HcbStoreProductCategory\Entity\Category')
                    ->createQueryBuilder('c')
                    ->select(array('cp.id'))
                    ->join('c.product', 'cp')
                    ->where('cp.enabled = 1')
                    ->andWhere('c = :category')->getDQL().')')
            ->setParameter('category', $localizedCategoryId);

        if (is_null($params)) return $qb;

        return $this->filtrationService->apply($params,
                                               $this->sortingService->apply($params, $qb, 'p'),
                                               'p');
    }
}
