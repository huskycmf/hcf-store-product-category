<?php
return array(
    'router' => array(
        'routes' => array(
            'hc-frontend' => array(
                'type' => 'HasLang',
                'options' => array(
                    'route' => '/'
                ),
                'child_routes' => array(
                    'category' => array(
                        'type' => 'segment',
                        'priority' => -1000,
                        'options' => array(
                            'route' => ':category',
                            'defaults' => array(
                                'controller' => 'HcfStoreProductCategory-Controller-Category'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'product' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/:product',
                                    'defaults' => array(
                                        'controller' => 'HcfStoreProductCategory-Controller-Category-Product'
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    ),
    'view_helpers'=> include __DIR__ . '/module/viewhelpers.config.php',
    'di' => include __DIR__ . '/module/di.config.php',

    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'HcfStoreProductCategory' => __DIR__ . '/../public',
            )
        )
    )
);
