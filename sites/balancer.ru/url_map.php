<?php

bors_url_map(array(
	'/cache/avatars/jpgto/(.+)\.jpg => balancer_cache_avatars_jpgto(1)',

	'/_tools/external/sites/preview => balancer_tools_external_sites_preview',

	'/users?/(\d+)/? => user_main(1)',

	'/memo/theo/mmorpg/ => balancer_page_dropbox',
));
