<?php
namespace HcfStoreProductCategory\Mvc\Router;

use HcbStoreProductCategory\Entity\Category\Localized as LocalizedEntity;
use HcfStoreProductCategory\Service\Collection\FetchQbBuilderService;
use Zend\Mvc\Router\RouteStackInterface;
use Zend\Mvc\Service\RouterFactory as DefaultRouterFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use HcCore\Entity\Locale as LocaleEntity;
use Zend\Stdlib\Parameters;

class RouterFactory extends DefaultRouterFactory
{
    /* @var \Zend\Di\Di */
    protected $di;

    /* @var \Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $cName
     * @param string $rName
     * @return \Zend\Mvc\Router\RouteStackInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $cName = null, $rName = null)
    {
        $router = parent::createService($serviceLocator, $cName, $rName);

        $this->di = $di = $serviceLocator->get('di');
        $this->em = $serviceLocator->get('Doctrine\ORM\EntityManager');

        /* @var $cacheStorage \Zend\Cache\Storage\StorageInterface */
        $cacheStorage = $serviceLocator->get('HcCore-CacheStorage');

        /* @var $currentLocale \HcCore\Entity\Locale */
        $currentLocale = $di->get('HcCore\Entity\Locale');

        $cacheId = "HcfStoreProductCategory_Category_Routes_".$currentLocale->getLocale();

        if ($cacheStorage->hasItem($cacheId)) {
            $routes = $cacheStorage->getItem($cacheId);
        } else {
            $routes = $this->getCategories($di->get('HcfStoreProductCategory\Service\Collection\FetchQbBuilderService',
                                                    array('entityManager'=>$this->em)),
                                           $currentLocale);
            $cacheStorage->setItem($cacheId, $routes);
        }

        foreach ($routes as $name => $route) {
            $router->addRoute($name, $route);
        }
        
        return $router;
    }

    /**
     * @param FetchQbBuilderService $fetchCategoryCollection
     * @param LocaleEntity $currentLocaleEntity
     */
    protected function getCategories(FetchQbBuilderService $fetchCategoryCollection,
                                     LocaleEntity $currentLocaleEntity)
    {
        $categoryQb = $fetchCategoryCollection
                           ->fetch(new Parameters(array('locale' => $currentLocaleEntity->getLocale())));

        $routes = array();

        /* @var $localizedEntity LocalizedEntity */
        foreach ($categoryQb->getQuery()->getResult() as $localizedEntity) {
            $routeId = 'category_'.$localizedEntity->getId();
            $literal = array( 'type' => 'Literal',
                              'options' => array(
                                'route' => $localizedEntity->getPage()->getUrl(),
                                'defaults' => array(
                                    'controller' => 'HcfStoreProductCategory-Controller-Category',
                                    'id' => $localizedEntity->getId()
                              )),
                              'may_terminate' => true,
                              'child_routes' => $this->getProductRoutes($localizedEntity)
                            );

            $routes[$routeId] = $literal;
        }

        return $routes;
    }

    /**
     * @param LocalizedEntity $localizedEntity
     * @return array
     */
    protected function getProductRoutes(LocalizedEntity $localizedEntity)
    {
        $localeEntity = $localizedEntity->getLocale();
        $routes = array();

        $products = $this->di->get('HcfStoreProductCategory\Service\Product\Collection\FetchQbBuilderService',
                                     array('entityManager'=>$this->em));

        /* @var $localizedProduct \HcbStoreProduct\Entity\Product\Localized */
        foreach ($products->fetch($localizedEntity)->getQuery()->getResult() as $localizedProduct) {
            if ($localizedProduct->getLocale()->getId() == $localeEntity->getId()) {
                $routes['product_'.$localizedProduct->getId()] = array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => $localizedProduct->getPage()->getUrl(),
                        'defaults' => array(
                            'controller' => 'HcfStoreProductCategory-Controller-Category-Product',
                            'id' => $localizedProduct->getId()
                        )
                    )
                );
                break;
            }
        }
        return $routes;
    }
}
