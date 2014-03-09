<?php

bors_url_map(array(
	'/cache/avatars/jpgto/(.+)\.jpg => balancer_cache_avatars_jpgto(1)',

	'/_tools/external/sites/preview => balancer_tools_external_sites_preview',
	'/(_cg/_st)/\w/\w/(\S+-\d+x\d+)\.png => balancer_tools_external_sites_preview(2,1)',
	'/(_cg/_st)/\w+/\w/[^/]+/(\S+-\d+x\d+)\.png => balancer_tools_external_sites_preview(2,1)',
	'/(_cg/_st)/\w/\w/(\S+)\.png => balancer_tools_external_sites_preview(2,1)',

	'/wc/\?(.+) => balancer_wc(1)',

	'/pages/ro/? => airbase_pages_ro',

	'/users?/(\d+)/? => user_main(1)',

	'/users/reputations/? => balancer_users_reputations',
	'(/users/)reputations/(\d+)\.html => balancer_users_reputations(NULL,2)',

	'/memo/theo/mmorpg/ => balancer_page_dropbox',

	'/forums/attaches/(\d+)/? => balancer_board_attaches_view(1)',

	'(/blog)/(\d{4})/? => balancer_blog_year(2)',
));
