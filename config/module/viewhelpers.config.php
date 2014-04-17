<?php
return array(
    'invokables' => array(
        'categoryProductUrl' => 'HcfStoreProductCategory\View\Helper\CategoryProductUrl',
        'categoryUrl' => 'HcfStoreProductCategory\View\Helper\CategoryUrl'
    ),

    'factories' => array(
        'categoryGetRouteParams' => function (Zend\View\HelperPluginManager $sm) {
                $di = $sm->getServiceLocator()->get('di');
                return $di->get('HcfStoreProductCategory\View\Helper\GetRouteParams');
        },
        'categoryGetProductRouteParams' => function (Zend\View\HelperPluginManager $sm) {
                $di = $sm->getServiceLocator()->get('di');
                return $di->get('HcfStoreProductCategory\View\Helper\GetProductRouteParams');
        }
    )
);
