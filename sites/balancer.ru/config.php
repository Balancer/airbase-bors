<?php

require(__DIR__.'/config-host.php');

config_set('default_template', 'blue_spring');

config_set('sitemap_classes', 'balancer_board_topic');
//config_set('cache_static', false); - чёрт, отключать, вроде как, и нельзя. Всякие звёздочки репутации и прочее...
config_set('classes_auto_base', 'balancer');
