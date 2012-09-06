<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;


// Here you can add additional smilies if you like (please note that you must escape singlequote and backslash)
$smiley_text = array(':)', '=)', ':|', '=|', ':(', '=(', ':D', '=D', ':o', ':O', ';)', ':/', ':P', ':lol:', ':mad:', ':rolleyes:', ':cool:');
$smiley_img = array('smile.png', 'smile.png', 'neutral.png', 'neutral.png', 'sad.png', 'sad.png', 'big_smile.png', 'big_smile.png', 'yikes.png', 'yikes.png', 'wink.png', 'hmm.png', 'tongue.png', 'lol.png', 'mad.png', 'roll.png', 'cool.png');

// Uncomment the next row if you add smilies that contain any of the characters &"'<>
//$smiley_text = array_map('pun_htmlspecialchars', $smiley_text);


//
// Make sure all BBCodes are lower case and do a little cleanup
//
function preparse_bbcode($text, &$errors, $is_signature = false)
{
	return $text;
}

include_once("engines/lcml/main.php");

//
// Parse message text
//
function parse_message($text, $hide_smilies, $nocache=false)
{
	global $pun_config, $lang_common, $pun_user, $cur_post;

	$ch = new Cache();
	if($ch->get("lcml-compiled", $text, "post://{$cur_post['id']}/") && !config('cache_disabled') && $nocacne==false)
		return $ch->last();

	$post_id    = intval(@$cur_post['id']);
	$post_time  = intval(@$cur_post['posted']);
	$post_year  = strftime("%Y", $post_time);
	$post_month = strftime("%m", $post_time);
	$post_day   = strftime("%d", $post_time);
	$post_hour  = strftime("%H", $post_time);
	$post_min   = strftime("%M", $post_time);

	$GLOBALS['main_uri'] = $GLOBALS['cms']['page_path'] = 
		"http://{$_SERVER['HTTP_HOST']}/$post_year/$post_month/$post_day/$post_hour/post-$post_id.html";

	if($nocache)
		config_set('lcml_cache_disable', true);
/*
	$body = lcml($text, 
		array(
			'cr_type' => 'save_cr',
			'forum_type' => 'punbb',
			'forum_base_uri' => 'http://www.balancer.ru/forum',
			'sharp_not_comment' => true,
			'html_disable' => true,
			'uri' => "post://{$cur_post['id']}/",
		)
	);
*/
	$body = pun_lcml($text, false);
	return $ch->set($body, 60*86400);
}
