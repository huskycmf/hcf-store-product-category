<?php
return array(
    'di' => include __DIR__ . '/module/di.config.php',

    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'HcfStoreProductCategory' => __DIR__ . '/../public',
            )
        )
    )
);
