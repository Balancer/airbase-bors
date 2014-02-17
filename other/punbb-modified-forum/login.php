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

// $forum_temporary_redirect = 'http://home.balancer.ru/mybb/thread-4.html';

if (isset($_GET['action']))
	define('PUN_QUIET_VISIT', 1);

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';

// Load the login.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/login.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (isset($_POST['form_sent']) && $action == 'in')
{
	$form_username = trim($_POST['req_username']);
	$form_password = trim($_POST['req_password']);

	$authorized = false;

	if(preg_match('!(login\.php|hactions/index.php|^index.php)!', $_POST['redirect_url']))
		$_POST['redirect_url'] = 'http://forums.balancer.ru/';

	config_set('redirect_to', $_POST['redirect_url']);
	$me = bors_user::do_login($form_username, $form_password, false);

	if($me === true)
		bors_exit();

	$authorized = is_object($me);

	if(!$me || !is_object($me) || !$authorized)
		message($lang_login['Wrong user/pass']." <a href=\"{$pun_config['root_uri']}/login.php?action=forget\">".$lang_login['Forgotten pass'].'</a>');

	$user_id = $me->id();


	// Update the status if this is the first time the user logged in

	$q = "SELECT u.*, g.*, o.logged, o.idle 
			FROM {$db->prefix}users AS u 
				INNER JOIN {$db->prefix}groups AS g ON u.group_id=g.g_id 
				LEFT JOIN {$db->prefix}online AS o ON o.user_id=u.id 
			WHERE u.id=".intval($user_id);

	$result = $db->query($q) 
		or error('Unable to fetch user information', __FILE__, __LINE__, $db->error());

	global $pun_user;
	$pun_user = $db->fetch_assoc($result);

	$group_id = $pun_user['g_id'];

	// Remove this users guest entry from the online list
	$db->query('DELETE FROM '.$db->prefix.'online WHERE ident=\''.$db->escape(get_remote_address()).'\'') or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());

	redirect(htmlspecialchars($_POST['redirect_url']), $lang_login['Login redirect']);
}

else if ($action == 'out')
{
	if ($pun_user['is_guest'] || !isset($_GET['id']) || $_GET['id'] != $pun_user['id'])
	{
		header('Location: http://forums.balancer.ru/');
		exit;
	}

	// Remove user from "users online" list.
	$db->query('DELETE FROM '.$db->prefix.'online WHERE user_id='.$pun_user['id']) or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());

	// Update last_visit (make sure there's something to update it with)
	if (isset($pun_user['logged']))
		$db->query('UPDATE '.$db->prefix.'users SET last_visit='.$pun_user['logged'].' WHERE id='.$pun_user['id']) or error('Unable to update user visit data', __FILE__, __LINE__, $db->error());

	bors()->changed_save();

	if($me = bors()->user())
		$me->do_logout();

	bors_exit();
//	redirect('index.php', $lang_login['Logout redirect']);
}


else if ($action == 'forget' || $action == 'forget_2')
{
	if (!$pun_user['is_guest'])
		header('Location: index.php');

	if (isset($_POST['form_sent']))
	{
		require PUN_ROOT.'include/email.php';

		// Validate the email-address
		$email = strtolower(trim($_POST['req_email']));
		if (!is_valid_email($email))
			message($lang_common['Invalid e-mail']);

		$user = bors_find_first('airbase_user', array('email' => $email, 'order' => '-id'));
		if(!$user)
			message($lang_login['No e-mail match'].' '.htmlspecialchars($email).'.');

		// Load the "activate password" template
		$mail_tpl = trim(file_get_contents(PUN_ROOT.'lang/'.$pun_user['language'].'/mail_templates/activate_password.tpl'));

		// The first row contains the subject
		$first_crlf = strpos($mail_tpl, "\n");
		$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
		$mail_message = trim(substr($mail_tpl, $first_crlf));

		// Do the generic replacements first (they apply to all e-mails sent out here)
		$mail_message = str_replace('<base_url>', $pun_config['o_base_url'].'/', $mail_message);
		$mail_message = str_replace('<board_mailer>', $pun_config['o_board_title'].' '.$lang_common['Mailer'], $mail_message);

		// Generate a new password and a new password activation code
		$new_password = random_pass(8);
		$new_password_key = random_pass(8);

		$user->set_activate_string($user->password_hashing($new_password));
		$user->set_activate_key($new_password_key);

		// Do the user specific replacements to the template
		$cur_mail_message = str_replace('<username>', $cur_hit['username'], $mail_message);
		$cur_mail_message = str_replace('<activation_url>', $pun_config['o_base_url'].'/profile.php?id='.$user->id().'&action=change_pass&key='.$new_password_key, $cur_mail_message);
		$cur_mail_message = str_replace('<new_password>', $new_password, $cur_mail_message);

		pun_mail($email, $mail_subject, $cur_mail_message);

		message($lang_login['Forget mail'].' <a href="mailto:'.$pun_config['o_admin_email'].'">'.$pun_config['o_admin_email'].'</a>.');
	}


	$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_login['Request pass'];
	$required_fields = array('req_email' => $lang_common['E-mail']);
	$focus_element = array('request_pass', 'req_email');
	require PUN_ROOT.'header.php';

?>
<div class="blockform">
	<h2><span><?php echo $lang_login['Request pass'] ?></span></h2>
	<div class="box">
		<form id="request_pass" method="post" action="login.php?action=forget_2" onsubmit="this.request_pass.disabled=true;if(process_form(this)){return true;}else{this.request_pass.disabled=false;return false;}">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_login['Request pass legend'] ?></legend>
					<div class="infldset">
						<input type="hidden" name="form_sent" value="1" />
						<input id="req_email" type="text" name="req_email" size="50" maxlength="50" />
						<p><?php echo $lang_login['Request pass info'] ?></p>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="request_pass" value="<?php echo $lang_common['Submit'] ?>" /><a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'];/*"*/ ?></a></p>
		</form>
	</div>
</div>
<?php

	require PUN_ROOT.'footer.php';
}


if (!$pun_user['is_guest'])
	header('Location: index.php');

// Try to determine if the data in HTTP_REFERER is valid (if not, we redirect to index.php after login)
$redirect_url = (isset($_SERVER['HTTP_REFERER']) && preg_match('#^'.preg_quote($pun_config['o_base_url']).'/(.*?)\.php#i', $_SERVER['HTTP_REFERER'])) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : 'index.php';

$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_common['Login'];
$required_fields = array('req_username' => $lang_common['Username'], 'req_password' => $lang_common['Password']);
$focus_element = array('login', 'req_username');
require PUN_ROOT.'header.php';

?>
<div class="blockform">
	<h2><span><?php echo $lang_common['Login'] ?></span></h2>
	<div class="box">
		<form id="login" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_login['Login legend'] ?></legend>
						<div class="infldset">
							<input type="hidden" name="form_sent" value="1" />
							<input type="hidden" name="redirect_url" value="<?php echo $redirect_url ?>" />
							<label class="conl"><strong><?php echo $lang_common['Username'] ?></strong><br /><input type="text" name="req_username" size="25" maxlength="25" tabindex="1" /><br /></label>
							<label class="conl"><strong><?php echo $lang_common['Password'] ?></strong><br /><input type="password" name="req_password" size="16" maxlength="16" tabindex="2" /><br /></label>
							<p class="clearb"><?php echo $lang_login['Login info'] ?></p>
							<p><a href="<?php echo $pun_config['root_uri'];?>/register.php" tabindex="4"><?php echo $lang_login['Not registered'] ?></a>&nbsp;&nbsp;
							<a href="<?php echo $pun_config['root_uri'];?>/login.php?action=forget" tabindex="5"><?php echo $lang_login['Forgotten pass'] ?></a></p>
						</div>
				</fieldset>
			</div>
			<p><input type="submit" name="login" value="<?php echo $lang_common['Login'];/*"*/?>" tabindex="3" /></p>
		</form>
	</div>
</div>
<?php

require PUN_ROOT.'footer.php';
