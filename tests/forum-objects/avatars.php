<?php

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
require_once(BORS_CORE.'/init.php');

config_set('is_developer', true);

$post = bors_load('balancer_board_post', 2304663);

$avatar = $post->avatar();
$image = $avatar->image();
//echo "image: {$image}\n";

$thumb = $image->thumbnail('50x');
/*
echo "thumb: {$thumb}\n";
echo "thumb->full_url(): {$thumb->full_url()}\n";
echo "thumb->url(): {$thumb->url()}\n";
echo "thumb->width() = {$thumb->width()}\n";

//echo $thumb->html_code()."\n";
*/

// {module class='balancer_board_modules_avatar' user_id=$v->target_user()->id() size='50'}
$avatar_module = bors_load('balancer_board_modules_avatar', NULL, array(
	'geo' => '50x50',
	'user_id' => 124,
///	'show_group' => false,
));

echo "--------------------\n{$avatar_module->html_code()}\n";

