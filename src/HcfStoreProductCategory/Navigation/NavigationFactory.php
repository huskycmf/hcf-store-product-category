<?php
namespace HcfStoreProductCategory\Navigation;

use HcBackend\Service\Alias\DetectAlias;
use HcbStoreProductCategory\Entity\Category as CategoryEntity;
use HcbStoreProductCategory\Entity\Category\Localized as CategoryLocalizedEntity;
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
                                DetectAlias $detectAliasService,
                                StorageInterface $storage)
    {
        $this->fetchCategoryCollectionService = $fetchCategoryCollection;
        $this->currentLocaleEntity = $currentLocaleEntity;
        $this->fetchCategoryProductCollectionService = $fetchCategoryProductCollectionService;
        $this->detectAliasService = $detectAliasService;
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

        /* @var $localizedEntity CategoryLocalizedEntity */
        foreach ($categoryQb->getQuery()->getResult() as $localizedEntity) {
            $categoryEntity = $localizedEntity->getCategory();

            $pageId = 'category_'.$categoryEntity->getId();
            $alias = $this->detectAliasService->detect($categoryEntity);

            $pages[$pageId] = array('label'=>$localizedEntity->getTitle(),
                                    'route'=>'hc-frontend/category',
                                    'class'=>$pageId,
                                    'pages'=>$this->getProductPages($localizedEntity),
                                    'params' => array(
                                        'category' => (is_null($alias) ?
                                                      $categoryEntity->getId() :
                                                      $alias->getAlias()->getName())));
        }

        $this->cacheStorage->setItem($cacheId, $pages);
        return $pages;
    }


    protected function getProductPages(CategoryLocalizedEntity $localizedEntity)
    {
        $localeEntity = $localizedEntity->getLocale();
        $pages = array();

        $qb = $this->fetchCategoryProductCollectionService->fetch($localizedEntity->getCategory());

        /* @var $product \HcbStoreProduct\Entity\Product */
        foreach ($qb->getQuery()->getResult() as $product) {

            $alias = $this->detectAliasService->detect($product);
            /* @var $localizedProduct \HcbStoreProduct\Entity\Product\Localized */
            foreach ($product->getLocalized() as $localizedProduct) {

                if ($localizedProduct->getLocale()->getId() == $localeEntity->getId()) {
                    $pages['product_'.$localizedProduct->getId()] =
                        array( 'label'=>$localizedProduct->getTitle(),
                               'route'=>'hc-frontend/category/product',
                               'params' => array(
                                  'product' => (is_null($alias) ?
                                               $product->getId() :
                                               $alias->getAlias()->getName())
                             ));
                }
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
