<?php

$topics_view_class = config('topics.view_class');

bors_url_map(array(
	"(/forum/)\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html => {$topics_view_class}(2,4)",
	'(/forum/)\d{4}/\d{1,2}/\d{1,2}/printable\-(\d+)\-\-.+\.html => forum_printable(2)',
));
