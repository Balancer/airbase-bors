<?php

bors_vhost_routes('tanzpol.org', [
	'(/news/\d{4}/\d{1,2}/\d{1,2}/)(\d+)\.html => tanzpol_news_page(2)',
]);
