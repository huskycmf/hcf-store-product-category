<?php
return array(
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
