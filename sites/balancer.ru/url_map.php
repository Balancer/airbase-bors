<?php

bors_url_map(array(
	'/_tools/external/sites/preview => balancer_tools_external_sites_preview',

	'/users?/(\d+)/? => user_main(1)',
));
