<?php
namespace HcfStoreProductCategory\Navigation;

use HcbStoreProductCategory\Entity\Category\Localized as LocalizedEntity;
use HcCore\Entity\Locale as LocaleEntity;
use HcfStoreProductCategory\Service\Collection\FetchQbBuilderService;
use HcfStoreProductCategory\Service\Fetch\Product\FetchByLocalizedCategoryService;
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
     * @var FetchQbBuilderService
     */
    protected $fetchCategoryCollection;

    /**
     * @var LocaleEntity
     */
    protected $currentLocaleEntity;

    /**
     * @var StorageInterface
     */
    protected $cacheStorage;

    /**
     * @var FetchByLocalizedCategoryService
     */
    protected $fetchByLocalizedCategoryService;

    /**
     * @param FetchQbBuilderService $fetchCategoryCollection
     * @param LocaleEntity $currentLocaleEntity
     * @param FetchByLocalizedCategoryService $fetchByLocalizedCategoryService
     * @param StorageInterface $storage
     */
    public function __construct(FetchQbBuilderService $fetchCategoryCollection,
                                LocaleEntity $currentLocaleEntity,
                                FetchByLocalizedCategoryService $fetchByLocalizedCategoryService,
                                StorageInterface $storage)
    {
        $this->fetchCategoryCollection = $fetchCategoryCollection;
        $this->currentLocaleEntity = $currentLocaleEntity;
        $this->fetchByLocalizedCategoryService = $fetchByLocalizedCategoryService;
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

        $categoryQb = $this->fetchCategoryCollection
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

        /* @var $localizedProduct \HcbStoreProduct\Entity\Product\Localized */
        foreach ($this->fetchByLocalizedCategoryService->fetch($localizedEntity) as $localizedProduct) {
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
