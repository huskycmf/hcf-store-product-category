<?php
namespace HcfStoreProductCategory\Navigation;

use HcbStoreProductCategory\Entity\Category\Localized as LocalizedEntity;
use HcCore\Entity\Locale as LocaleEntity;
use HcfStoreProductCategory\Service\Collection\FetchQbBuilderService as FetchCategoryCollectionService;
use HcfStoreProductCategory\Service\Product\Collection\FetchQbBuilderService as FetchCategoryProductCollectionService;
use Zend\Cache\Storage\StorageInterface;
use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;

/**
 * Constructed factory to set pages during construction.
 */
class NavigationFactory extends AbstractNavigationFactory
{
    /**
     * @var FetchCategoryCollectionService
     */
    protected $fetchCategoryCollectionService;

    /**
     * @var LocaleEntity
     */
    protected $currentLocaleEntity;

    /**
     * @var StorageInterface
     */
    protected $cacheStorage;

    /**
     * @var FetchCategoryProductCollectionService
     */
    protected $fetchCategoryProductCollectionService;

    /**
     * @param FetchCategoryCollectionService $fetchCategoryCollection
     * @param LocaleEntity $currentLocaleEntity
     * @param FetchCategoryProductCollectionService $fetchCategoryProductCollectionService
     * @param StorageInterface $storage
     */
    public function __construct(FetchCategoryCollectionService $fetchCategoryCollection,
                                LocaleEntity $currentLocaleEntity,
                                FetchCategoryProductCollectionService $fetchCategoryProductCollectionService,
                                StorageInterface $storage)
    {
        $this->fetchCategoryCollectionService = $fetchCategoryCollection;
        $this->currentLocaleEntity = $currentLocaleEntity;
        $this->fetchCategoryProductCollectionService = $fetchCategoryProductCollectionService;
        $this->cacheStorage = $storage;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return array|null|\Zend\Config\Config
     */
    public function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $this->pages = $this->preparePages($serviceLocator, $this->getCategories());
        }
        return $this->pages;
    }

    /**
     * @param LocaleEntity $currentLocaleEntity
     */
    protected function getCategories()
    {
        $cacheId = "HcfStoreProductCategory_Category_Navigation_".$this->currentLocaleEntity->getLocale();

        if ($this->cacheStorage->hasItem($cacheId)) {
            return $this->cacheStorage->getItem($cacheId);
        }

        $categoryQb = $this->fetchCategoryCollectionService
                            ->fetch(new Parameters(array('locale' => $this->currentLocaleEntity->getLocale())));

        $pages = array();

        /* @var $localizedEntity LocalizedEntity */
        foreach ($categoryQb->getQuery()->getResult() as $localizedEntity) {
            $pageId = 'category_'.$localizedEntity->getId();

            $pages[$pageId] = array('label'=>$localizedEntity->getTitle(),
                                    'route'=>$pageId,
                                    'pages'=>$this->getProductPages($pageId, $localizedEntity));
        }

        $this->cacheStorage->setItem($cacheId, $pages);
        return $pages;
    }

    /**
     * @param string $categoryRoute
     * @param LocalizedEntity $localizedEntity
     * @return array
     */
    protected function getProductPages($categoryRoute, LocalizedEntity $localizedEntity)
    {
        $localeEntity = $localizedEntity->getLocale();
        $pages = array();

        $qb = $this->fetchCategoryProductCollectionService->fetch($localizedEntity);

        /* @var $localizedProduct \HcbStoreProduct\Entity\Product\Localized */
        foreach ($qb->getQuery()->getResult() as $localizedProduct) {
            if ($localizedProduct->getLocale()->getId() == $localeEntity->getId()) {
                $pageId = 'product_'.$localizedProduct->getId();
                $pages[$pageId] = array( 'label'=>$localizedProduct->getTitle(),
                                         'route'=>$categoryRoute.'/'.$pageId);
                break;
            }
        }
        return $pages;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'constructed';
    }
}
