<?php

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
require_once(BORS_CORE.'/init.php');

config_set('is_developer', true);

$post = bors_load('balancer_board_post', 2304663);

$avatar = $post->avatar();
$image = $avatar->image();
echo "image: {$image}\n";

$thumb = $image->thumbnail('50x');

echo "thumb: {$thumb}\n";
echo "thumb->full_url(): {$thumb->full_url()}\n";
echo "thumb->url(): {$thumb->url()}\n";

echo $thumb->html_code()."\n";

