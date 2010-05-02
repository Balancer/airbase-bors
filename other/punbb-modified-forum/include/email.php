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


//
// Validate an e-mail address
//
function is_valid_email($email)
{
	if (strlen($email) > 50)
		return false;

	return preg_match('/^(([^<>()[\]\\.,;:\s@"\']+(\.[^<>()[\]\\.,;:\s@"\']+)*)|("[^"\']+"))@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\])|(([a-zA-Z\d\-]+\.)+[a-zA-Z]{2,}))$/', $email);
}


//
// Check if $email is banned
//
function is_banned_email($email)
{
	global $db, $pun_bans;

	foreach ($pun_bans as $cur_ban)
	{
		if ($cur_ban['email'] == '')
			continue;

		if ($email == $cur_ban['email'])
			return true;
		if(strpos($cur_ban['email'], '@') === false && stristr($email, '@'.$cur_ban['email']))
			return true;
	}

	return false;
}


//
// Wrapper for PHP's mail()
//
function pun_mail($to, $subject, $message, $from = '')
{
//	echo "=<xmp>$from</xmp>="; exit();
	require_once('engines/mail.php');

	global $pun_config, $lang_common;

	// Default sender/return address
	if (!$from)
		$from = '"'.str_replace('"', '', $pun_config['o_board_title'].' '.$lang_common['Mailer']).'" <'.$pun_config['o_webmaster_email'].'>';

	// Do a little spring cleaning
	$to = trim(preg_replace('#[\n\r]+#s', '', $to));
	$subject = trim(preg_replace('#[\n\r]+#s', '', $subject));
	$from = trim(preg_replace('#[\n\r:]+#s', '', $from));

//	$headers = 'From: '.$from."\r\n".'Date: '.date('r')."\r\n".'MIME-Version: 1.0'."\r\n".'Content-transfer-encoding: 8bit'."\r\n".'Content-type: text/plain; charset='.$lang_common['lang_encoding']."\r\n".'X-Mailer: PunBB Mailer';

	// Make sure all linebreaks are CRLF in message
	$message = str_replace("\n", "\r\n", pun_linebreaks($message));

	send_mail($to, $subject, $message, NULL, $from, array(
		'X-Mailer' => 'PunBB forum mailer over BORS(c)',
	));
}


//
// This function was originally a part of the phpBB Group forum software phpBB2 (http://www.phpbb.com).
// They deserve all the credit for writing it. I made small modifications for it to suit PunBB and it's coding standards.
//
function server_parse($socket, $expected_response)
{
	$server_response = '';
	while (substr($server_response, 3, 1) != ' ')
	{
		if (!($server_response = fgets($socket, 256)))
			error('Couldn\'t get mail server response codes. Please contact the forum administrator.', __FILE__, __LINE__);
	}

	if (!(substr($server_response, 0, 3) == $expected_response))
		error('Unable to send e-mail. Please contact the forum administrator with the following error message reported by the SMTP server: "'.$server_response.'"', __FILE__, __LINE__);
}
