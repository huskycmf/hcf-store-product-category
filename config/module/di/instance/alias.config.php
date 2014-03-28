<?php
return array_merge_recursive(
    include __DIR__.'/alias/controller.config.php',
    include __DIR__.'/alias/common.config.php',
    include __DIR__.'/alias/service.config.php',
    include __DIR__.'/alias/data.config.php'
);
