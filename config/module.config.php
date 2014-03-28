<?php
return array(
//  'router' => include __DIR__ . '/module/router.config.php',
    'di' => include __DIR__ . '/module/di.config.php',

    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'HcfStoreProductCategory' => __DIR__ . '/../public',
            )
        )
    )
);
