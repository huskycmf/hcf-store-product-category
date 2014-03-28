<?php
return array_merge_recursive(
    include __DIR__.'/instance/controller.config.php',
    include __DIR__.'/instance/common.config.php',
    include __DIR__.'/instance/service.config.php',
    include __DIR__.'/instance/data.config.php'
);
