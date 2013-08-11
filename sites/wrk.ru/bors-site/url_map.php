<?php

bors_url_map(array(
	'/ => wrk_main',
	'/blogs/ => wrk_blogs_main',
	'/login/ => wrk_mauth_login',
	'/~(\w)(\w+) => wrk_go(1,2)',
));
