<?php
return array(
    'allowed_controllers' => include __DIR__ . '/di/allowed_controllers.config.php',
    'instance' => array_merge_recursive( array(
        'alias'=> include __DIR__ . '/di/instance/alias.config.php',
        'preference' => include __DIR__ . '/di/instance/preference.config.php'
    ), include __DIR__ . '/di/instance/instance.config.php')
);
