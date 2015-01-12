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

$GLOBALS['cms']['cant_lock'] = true;

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'include/attach/attach_incl.php'; //Attachment Mod row, loads variables, functions and lang file

if(bors_stop_bots('__nobots_testing', 'edit'))
	return;

$GLOBALS['cms']['cache_disabled'] = true;
config_set('cache_disabled' , true);
config_set('lcml_cache_disable_full', true);

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view']);


if($is_banned)
	message("У Вас нет доступа к этой возможности до ".strftime("%Y-%m-%d %H:%M", WARNING_DAYS*86400+$ban_expire)
		.'<br/><br/>'.bbf_bans::message_ls()
	);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang_common['Bad request']);

// Fetch some info about the post, the topic and the forum
$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, 
		fp.post_replies, fp.post_topics, 
		t.id AS tid, t.subject, t.posted, t.closed, t.description, t.keywords_string,
		p.poster, p.poster_id, p.hide_smilies
	FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id) 
	or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang_common['Bad request']);

$cur_post = $db->fetch_assoc($result);
$blog = bors_load('balancer_board_blog', $id);
$post = bors_load('balancer_board_post', $id);
$topic = $post->topic();
$forum = $topic->forum();

$me = bors()->user();

$cur_post['message'] = $post->source();

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_post['moderators'] != '') ? unserialize($cur_post['moderators']) : array();
$is_admmod = ($pun_user['g_id'] == PUN_ADMIN || ($pun_user['g_id'] == PUN_MOD && array_key_exists($pun_user['username'], $mods_array))) ? true : false;

// Determine whether this post is the "topic post" or not
$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$cur_post['tid'].' ORDER BY posted LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
$topic_post_id = $db->result($result);

$can_edit_subject = ($id == $topic_post_id && (($pun_user['g_edit_subjects_interval'] == '0' || (time() - $cur_post['posted']) < $pun_user['g_edit_subjects_interval']) || $is_admmod)) ? true : false;

// Do we have permission to edit this post?
if (($pun_user['g_edit_posts'] == '0' ||
	$cur_post['poster_id'] != $pun_user['id'] ||
	$cur_post['closed'] == '1') &&
	!$is_admmod)
	message($lang_common['No permission']);

// Load the post.php/edit.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/post.php';

// Start with a clean slate
$errors = array();

if (isset($_POST['form_sent']))
{
	if($msg = $post->is_edit_disable())
		return bors_message($msg);

	if ($is_admmod)
		confirm_referrer('edit.php');

	// If it is a topic it must contain a subject
	if ($can_edit_subject)
	{
		$subject = pun_trim($_POST['req_subject']);

		if ($subject == '')
			$errors[] = $lang_post['No subject'];
		else if (pun_strlen($subject) > 255)
			$errors[] = $lang_post['Too long subject'];
		else if ($pun_config['p_subject_all_caps'] == '0' && strtoupper($subject) == $subject && $pun_user['g_id'] > PUN_MOD)
			$subject = ucwords(strtolower($subject));
	}

	// Clean up message from POST
	$message = pun_linebreaks(pun_trim($_POST['req_message']));

	if ($message == '')
		$errors[] = $lang_post['No message'];
	else if (strlen($message) > 65535)
		$errors[] = $lang_post['Too long message'];
	else if ($pun_config['p_message_all_caps'] == '0' && strtoupper($message) == $message && $pun_user['g_id'] > PUN_MOD)
		$message = ucwords(strtolower($message));

	// Validate BBCode syntax
	if ($pun_config['p_message_bbcode'] == '1' && strpos($message, '[') !== false && strpos($message, ']') !== false)
	{
		require PUN_ROOT.'include/parser.php';
		$message = preparse_bbcode($message, $errors);
	}

	$hide_smilies = isset($_POST['hide_smilies']) ? intval($_POST['hide_smilies']) : 0;
	if ($hide_smilies != '1') $hide_smilies = '0';

	// Did everything go according to plan?
	if (empty($errors) && !isset($_POST['preview']))
	{
		require PUN_ROOT.'include/search_idx.php';

		if ($can_edit_subject)
		{
			// Update the topic and any redirect topics
			$db->query('UPDATE '.$db->prefix.'topics SET subject=\''.$db->escape($subject).'\' WHERE id='.$cur_post['tid'].' OR moved_to='.$cur_post['tid'])
				or error('Unable to update topic', __FILE__, __LINE__, $db->error());
		}

		$topic->set_last_edit_time(time(), true);

		if($can_edit_subject)
		{
			$topic->set_description($_POST['description'], true);
			$topic->set_keywords_string($_POST['keywords_string'], true);
		}

		$post->set_source($message, true);
		$post->set_hide_smilies(intval($hide_smilies), true);
		$post->set_have_attach(NULL, true);

		if($me && $me->is_coordinator())
		{
			if(empty($_POST['is_moderatorial']))
			{
				$post->set_is_moderatorial(0, true);
			}
			else
			{
				if(!$post->is_moderatorial())
					balancer_board_action::add($topic, "Административное предупреждение: {$post->nav_named_link()}", true);

				$post->set_is_moderatorial(1, true);
			}
		}

		//Attachment Mod 2.0 Block Start
		//First check if there are any files to delete, the postvariables should be named 'attach_delete_'.$i , if it's set you're going to delete the value of this (the 0 =< $i < attachments, just to get some order in there...)
		if(isset($_POST['attach_num_attachments'])){
			// if there is any number of attachments, check if there has been any deletions ... if so, delete the files if allowed...
			$attach_num_attachments = intval($_POST['attach_num_attachments']);
			for($i=0;$i<$attach_num_attachments;$i++){
				if(array_key_exists('attach_delete_'.$i,$_POST)){
					$attach_id=intval($_POST['attach_delete_'.$i]);
					//fetch info about it ... owner and such ... (so we know if it's going to be ATTACH_OWNER_DELETE or ATTACH_DELETE that will affect the rulecheck...
					$result_attach = $db->query('SELECT af.owner,ar.rules FROM '.$db->prefix.'attach_2_files AS af, '.$db->prefix.'attach_2_rules AS ar, '.$db->prefix.'posts AS p, '.$db->prefix.'topics AS t WHERE af.id=\''.intval($attach_id).'\' AND ar.group_id=\''.intval($pun_user['g_id']).'\' AND (ar.forum_id=t.forum_id OR ar.forum_id=0) AND t.id=p.topic_id AND p.id=af.post_id ORDER BY ar.forum_id DESC LIMIT 1')
						or error('Unable to fetch attachment details and forum rules', __FILE__, __LINE__, $db->error());
					if($db->num_rows($result_attach)>0||$pun_user['g_id']==PUN_ADMIN){
						list($attach_cur_owner,$attach_rules)=$db->fetch_row($result_attach);

						$attach_allowed = false;

						if($pun_user['g_id']==PUN_ADMIN)//admin overrides
							$attach_allowed = true;
						elseif($attach_cur_owner==$pun_user['id'])//it's the owner of the file that want to delete it
							$attach_allowed=attach_rules($attach_rules,ATTACH_OWNER_DELETE);
						else //it's not the owner that wants to delete the attachment...
							$attach_allowed=attach_rules($attach_rules,ATTACH_DELETE);

						if($attach_allowed){
							if(!attach_delete_attachment($attach_id)){
								// uncomment if you want to show error if it fails to delete
								//error('Unable to delete attachment.');
							}
						}else{
							// the user may not delete it ... uncomment the error if you want to use it ...
							//error('You\'re not allowed to delete the attachment');
						}
					}else{
						// the user probably hasn't any rules in this forum any longer...
					}
				}
			}
		}

		//Then recieve any potential new files
		if((isset($_FILES['attached_file'])
			&&$_FILES['attached_file']['size']!=0
			&&is_uploaded_file($_FILES['attached_file']['tmp_name'])))
		{
			//ok, we have a new file, much similar to post, except we need to check if the user uploads too many files...
			$attach_allowed=false;
			if($pun_user['g_id']==PUN_ADMIN)
			{
				$attach_allowed=true;
			}
			else
			{
				//fetch forum rules and the number of attachments for this post.
				$result_attach = $db->query('SELECT COUNT(af.id) FROM '.$db->prefix.'attach_2_files AS af WHERE af.post_id = \''.intval($id).'\' GROUP BY af.post_id LIMIT 1')
					or error('Unable to fetch current number of attachments in post',__FILE__,__LINE__,$db->error());	
				if($db->num_rows($result_attach)==1)
				{
					list($attach_num_attachments)=$db->fetch_row($result_attach);
				}
				else
				{
					$attach_num_attachments=0;
				}

				$result_attach = $db->query('SELECT ar.rules,ar.size,ar.file_ext,ar.per_post FROM '.$db->prefix.'attach_2_rules AS ar, '.$db->prefix.'posts AS p, '.$db->prefix.'topics AS t WHERE group_id=\''.intval($pun_user['g_id']).'\' AND p.id = \''.intval($id).'\' AND t.id = p.topic_id AND (ar.forum_id = t.forum_id OR ar.forum_id=0) ORDER BY ar.forum_id DESC LIMIT 1')
					or error('Unable to fetch attachment rules',__FILE__,__LINE__,$db->error());

				if($db->num_rows($result_attach)==1)
				{
					list($attach_rules,$attach_size,$attach_file_ext,$attach_per_post)=$db->fetch_row($result_attach);
					//first check if the user is allowed to upload
					$attach_allowed = attach_allow_upload($attach_rules,$attach_size,$attach_file_ext,$_FILES['attached_file']['size'],$_FILES['attached_file']['name']); //checks so that extensions, size etc is ok
					if($attach_allowed && $attach_num_attachments < $attach_per_post) // if we haven't attached too many...
						$attach_allowed = true;
					else
						$attach_allowed = false;
				}
				else
				{
					// probably no rules, don't allow upload
					$attach_allowed = false;
				}
			}
			// ok, by now we should know if it's allowed to upload or not ... 
			if($attach_allowed)
			{	//if so upload it ... 
				if(!attach_create_attachment(
					$_FILES['attached_file']['name'],
					$_FILES['attached_file']['type'],
					$_FILES['attached_file']['size'],
					$_FILES['attached_file']['tmp_name'],
					$id,
					count_chars($message)
				))
				{
					error('Error creating attachment, inform the owner of this bulletin board of this problem. (Most likely something to do with rights on the filesystem)',__FILE__,__LINE__);
				}
			}
		}
		//Attachment Mod 2.0 Block End

		if(!isset($_POST['silent']) || !$is_admmod)
		{
			$post->set_edited(time(), true);
			$post->set_edited_by($pun_user['username'], true);
		}

		include_once("engines/bors.php");

		$post->set_modify_time(time(), true);
		$topic->set_modify_time(time(), true);
		$topic->set_last_post_create_time(max($topic->last_post_create_time(), $post->create_time()), true);

		// Если эту фигню удалять, то надо проверить на аттачи и множественные аттачи, как при постинге, так и при редактировании
		config_set('lcml_cache_disable', true);

		$post->set_body(NULL);

		$post->store();
		$topic->store();

		$post->recalculate($topic);
		$post->cache_clean_self();

		$post->body();

//		Почему-то с этим ссылки нормально не утягиваются при редактировании.
//		http://www.balancer.ru/g/p3637263 и т.п.
//		$post->full_recalculate_and_clean();

		$page = $topic->page_by_post_id($post->id());
		$topic->set_page($page);

		if(empty($_POST['as_blog']))
		{
			// Если метки «блог» нет, но есть предыдущая запись блога, то удаляем.
			if($blog)
			{
				$blog->delete();
			}
		}
		else
		{
			if(!$blog)
			{
				$blog = new balancer_board_blog($post->id());
				$blog->new_instance();
			}

			$blog->set_owner_id($post->owner_id(), true);
			$blog->set_forum_id($topic->forum_id(), true);
			$blog->set_is_public($topic->is_public(), true);
			$blog->set_keywords_string($_POST['blog_keywords_string'], true);
			$blog->cache_clean();
		}

		if(empty($_POST['export_blog']))
		{
			// Если метки «транслировать» нет, то удаляем.
			include_once('engines/blogs/livejournal.com.php');
			bors_blog_livejournal_com_delete($post->owner_id(), $post);
		}
		else
		{
			// Иначе делаем кросспост
			include_once('engines/blogs/livejournal.com.php');
			bors_blog_livejournal_com_edit(
				$post->owner_id(),
				$topic,
				$topic->first_post()->id() == $post->id() ? $topic : $post,
				$post,
				object_property($blog, 'keywords_string') ? $blog : $topic
			);
		}

		$topic->cache_clean_self();

		$post->cache_clean();

		redirect('viewtopic.php?pid='.$id.'#p'.$id, $lang_post['Edit redirect']);
	}
}

//Attachment Mod 2.0 Block Start
//ok, first check the rules, so we know if the user may may upload more or delete potentially existing attachments
$attach_allow_delete=false;
$attach_allow_owner_delete=false;
$attach_allow_upload=false;
$attach_allowed=false;
$attach_allow_size=0;
$attach_per_post=0;
if($pun_user['g_id']==PUN_ADMIN)
{
	$attach_allow_delete=true;
	$attach_allow_owner_delete=true;
	$attach_allow_upload=true;
	$attach_allow_size=$pun_config['attach_max_size'];
	$attach_per_post=-1;
}
else
{
	$result_attach=$db->query('SELECT ar.rules,ar.size,ar.per_post,COUNT(f.id) FROM '.$db->prefix.'attach_2_rules AS ar, '.$db->prefix.'attach_2_files AS f, '.$db->prefix.'posts AS p, '.$db->prefix.'topics AS t WHERE group_id=\''.intval($pun_user['g_id']).'\' AND p.id = \''.intval($id).'\' AND t.id = p.topic_id AND (ar.forum_id = t.forum_id OR ar.forum_id=0) GROUP BY f.post_id ORDER BY ar.forum_id DESC LIMIT 1')
		or error('Unable to fetch attachment rules and current number of attachments in post (#2)',__FILE__,__LINE__,$db->error());	

	if($db->num_rows($result_attach)==1)
	{
		list($attach_rules,$attach_allow_size,$attach_per_post,$attach_num_attachments)=$db->fetch_row($result_attach);
		//may the user delete others attachments?
		$attach_allow_delete = attach_rules($attach_rules,ATTACH_DELETE);
		//may the user delete his/her own attachments?
		$attach_allow_owner_delete = attach_rules($attach_rules,ATTACH_OWNER_DELETE);
		//may the user upload new files?
		$attach_allow_upload = attach_rules($attach_rules,ATTACH_UPLOAD);
	}
	else
	{
		//no rules set, so nothing allowed
	}
}

$attach_output = '';
$attach_output_two = '';
//check if this post has attachments, if so make the appropiate output
if($attach_allow_delete||$attach_allow_owner_delete||$attach_allow_upload)
{
	$attach_allowed=true;
	$result_attach=$db->query('SELECT af.id, af.owner, af.filename, af.extension, af.size, af.downloads FROM '.$db->prefix.'attach_2_files AS af WHERE post_id=\''.intval($id).'\'')
		or error('Unable to fetch current attachments',__FILE__,__LINE__,$db->error());

	if($db->num_rows($result_attach)>0)
	{
		//time for some output ... create the existing files ... 
		$i=0;
		while(list($attach_id,$attach_owner,$attach_filename,$attach_extension,$attach_size,$attach_downloads)=$db->fetch_row($result_attach))
		{
			if(($attach_owner==$pun_user['id']&&$attach_allow_owner_delete)||$attach_allow_delete)
				$attach_output .= '<br />'."\n".'<input type="checkbox" name="attach_delete_'.$i.'" value="'.$attach_id.'" />'.$lang_attach['Delete?'].' '.attach_icon($attach_extension).' <a href="./attachment.php?item='.$attach_id.'">'.$attach_filename.'</a>, '.$lang_attach['Size:'].' '.number_format($attach_size).' '.$lang_attach['bytes'].', '.$lang_attach['Downloads:'].' '.number_format($attach_downloads);
			else
				$attach_output_two .= '<br />'."\n".attach_icon($attach_extension).' <a href="./attachment.php?item='.$attach_id.'">'.$attach_filename.'</a>, '.$lang_attach['Size:'].' '.number_format($attach_size).' '.$lang_attach['bytes'].', '.$lang_attach['Downloads:'].' '.number_format($attach_downloads);
			$i++;
		}

		if(strlen($attach_output)>0)
			$attach_output = '<input type="hidden" name="attach_num_attachments" value="'.$db->num_rows($result_attach).'" />'.$lang_attach['Existing'] . $attach_output;

		if(strlen($attach_output_two)>0)
			$attach_output .= "<br />\n".$lang_attach['Existing2'] . $attach_output_two;

		$attach_output .= "<br />\n";
	}
	else
	{
		// we have not existing files
	}
}

//fix the 'new upload' field...
if($attach_allow_upload)
{
	if(strlen($attach_output)>0)
		$attach_output .= "<br />\n";

	if($attach_per_post==-1)
		$attach_per_post = '<em>unlimited</em>';

	$attach_output .= str_replace('%%ATTACHMENTS%%', $attach_per_post, $lang_attach['Upload'])."<br />\n"
		.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$attach_allow_size.'" />'
		.'<input type="file" name="attached_file" />';
}
//Attachment Mod 2.0 Block End


$page_title = pun_htmlspecialchars($pun_config['o_board_title']).' / '.$lang_post['Edit post'];
$required_fields = array('req_subject' => $lang_common['Subject'], 'req_message' => $lang_common['Message']);
$focus_element = array('edit', 'req_message');

$header[] = "<script type=\"text/javascript\" src=\"/_bors3rdp/js/flowplayer-3.2.12/flowplayer-3.2.11.min.js\"></script>";

include('include/tinymce.php');
require PUN_ROOT.'header.php';

$cur_index = 1;

?>
<div class="linkst">
	<div class="inbox">
		<ul>
			<li><a href="<?php echo $pun_config['root_uri'];?>/index.php"><?php echo $lang_common['Index'] ?></a></li>
			<li>&nbsp;&raquo;&nbsp;<a href="viewforum.php?id=<?php echo $cur_post['fid'] /*"*/?>"><?php echo pun_htmlspecialchars($cur_post['forum_name']) ?></a></li>
			<li>&nbsp;&raquo;&nbsp;<a href="<?= $post->url_for_igo();/*"*/?>"><?= pun_htmlspecialchars($cur_post['subject']) ?></a></li></ul>
	</div>
</div>

<?php

// If there are errors, we display them
if (!empty($errors))
{

?>
<div id="posterror" class="block">
	<h2><span><?php echo $lang_post['Post errors'] ?></span></h2>
	<div class="box">
		<div class="inbox"
			<p><?php echo $lang_post['Post errors info'] ?></p>
			<ul>
<?php

	while (list(, $cur_error) = each($errors))
		echo "\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
?>
			</ul>
		</div>
	</div>
</div>

<?php

}
else if (isset($_POST['preview']))
{
	require_once PUN_ROOT.'include/parser.php';
	$preview_message = parse_message($message, $hide_smilies, true);

?>
<div id="postpreview" class="blockpost">
	<h2><span><?php echo $lang_post['Post preview'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<div class="postright">
				<div class="postmsg">
					<?php echo $preview_message."\n" ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

}

?>

<div class="blockform">
	<h2><?php echo $lang_post['Edit post'] ?></h2>
<?php
if($msg = $post->is_edit_disable())
	echo "<div class=\"warning_note\">{$msg}</div>";
?>

	<div class="box">
		<form id="edit" method="post" <?php echo 'enctype="multipart/form-data"'; ##Attachment Mod 2.0 ?> action="edit.php?id=<?php echo $id ?>&amp;action=edit" onsubmit="return process_form(this)">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_post['Edit post legend'] ?></legend>
					<input type="hidden" name="form_sent" value="1" />
					<div class="infldset txtarea">
<?php if ($can_edit_subject): ?>
	<label><?php echo $lang_common['Subject'] ?><br /><input class="longinput" type="text" name="req_subject" size="80" maxlength="255" tabindex="<?php echo $cur_index++ ?>" value="<?php echo pun_htmlspecialchars(isset($_POST['req_subject']) ? $_POST['req_subject'] : $cur_post['subject'])/*"*/ ?>" /><br /></label>
	<label>Описание темы<br/><input class="longinput" type="text" name="description" size="80" maxlength="255" tabindex="<?php echo $cur_index++ ?>" value="<?php echo pun_htmlspecialchars(isset($_POST['decription']) ? $_POST['description'] : $cur_post['description']) ?>" /><br /></label>
	<label>Ключевые слова (через запятую)<br/><input class="longinput" type="text" name="keywords_string" size="80" maxlength="255" tabindex="<?php echo $cur_index++ ?>" value="<?php echo pun_htmlspecialchars(isset($_POST['keywords_string']) ? $_POST['keywords_string'] : $cur_post['keywords_string'] ? $cur_post['keywords_string'] : $forum->keywords_string()) ?>" /><br /></label>
<?php endif; ?>
<?php if($blog): ?>
	<label>Ключевые слова для записи блога<br/><input class="longinput" type="text" name="blog_keywords_string" size="80" maxlength="255" tabindex="<?php echo $cur_index++ ?>" value="<?php echo pun_htmlspecialchars(isset($_POST['blog_keywords_string']) ? $_POST['keywords_string'] : $blog->keywords_string()) /*"*/ ?>" /><br /></label>
<?php else: ?>
	<div id="here_keywords"></div>
<?php endif; ?>
	<label><?php echo $lang_common['Message'] ?><br />

<?php
$profile = config('client_profile');

if(!$profile || $profile->textarea_type() == 'markitup')
{
	?>
<div id="emoticons">
	<a href="#" title=":)"><img src="http://www.airbase.ru/forum/smilies/smile.gif" /></a>
	<a href="#" title=":("><img src="http://www.airbase.ru/forum/smilies/frown.gif" /></a>
	<a href="#" title=":eek:"><img src="http://www.airbase.ru/forum/smilies/eek.gif" /></a>
	<a href="#" title=":p"><img src="http://www.airbase.ru/forum/smilies/tongue.gif" /></a>
	<a href="#" title=";)"><img src="http://www.airbase.ru/forum/smilies/wink.gif" /></a>
	<a href="#" title=":D"><img src="http://www.airbase.ru/forum/smilies/biggrin.gif" /></a>
</div>
	<?php
}
	?>

						<textarea name="req_message" id="bbcode" rows="20" cols="95" tabindex="<?php echo $cur_index++ ?>"><?php echo pun_htmlspecialchars(isset($_POST['req_message']) ? $message : $cur_post['message']) ?></textarea><br /></label>
						<ul class="bblinks">
							<li><a href="<?php echo $pun_config['root_uri'];?>/help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang_common['BBCode'] ?></a>: <?php echo ($pun_config['p_message_bbcode'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><a href="<?php echo $pun_config['root_uri'];?>/help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang_common['img tag'] ?></a>: <?php echo ($pun_config['p_message_img_tag'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><a href="<?php echo $pun_config['root_uri'];?>/help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang_common['Smilies'] ?></a>: <?php echo ($pun_config['o_smilies'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
						</ul>
					</div>
				</fieldset>
<?php

//Attachment Mod Block Start
if($attach_allowed){
?>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_attach['Attachment'] ?></legend>
					<div class="infldset">
						<div class="rbox">
							<?php echo $attach_output; ?><br />
							<?php echo $lang_attach['Note2']; ?>
						</div>
					</div>
				</fieldset>
<?php
}
//Attachment Mod Block End

$checkboxes = array();
if ($pun_config['o_smilies'] == '1')
{
	if (isset($_POST['hide_smilies']) || $cur_post['hide_smilies'] == '1')
		$checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" checked="checked" tabindex="'.($cur_index++).'" />&nbsp;'.$lang_post['Hide smilies'];
	else
		$checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'" />&nbsp;'.$lang_post['Hide smilies'];
}

if ($is_admmod)
{
	if ((isset($_POST['form_sent']) && isset($_POST['silent'])) || !isset($_POST['form_sent']))
		$checkboxes[] = '<label><input type="checkbox" name="silent" value="1" tabindex="'.($cur_index++).'" checked="checked" />&nbsp;'.$lang_post['Silent edit'];
	else
		$checkboxes[] = '<label><input type="checkbox" name="silent" value="1" tabindex="'.($cur_index++).'" />&nbsp;'.$lang_post['Silent edit'];
}

//print_d($blog);

$checkboxes[] = "<label><input type=\"checkbox\" name=\"as_blog\"     value=\"1\" tabindex=\"".($cur_index++).'"'
	.($blog ? ' checked="checked"' : '')
	." onClick=\"getElementById('here_keywords').innerHTML= this.checked ? '"
		.addslashes("<label><strong>Теги:</strong>&nbsp;<input class='longinput' type='text' name='blog_keywords_string' value='"
			.htmlspecialchars(defval($_POST, 'blog_keywords_string', $topic->keywords_string()))
			."' size='40' maxlength='255' /><br /></label>")
		."' : ''\"/>Разместить ответ в <a href=\"http://www.balancer.ru/user/{$pun_user['id']}/blog/\">Вашем блоге</a>";
//$checkboxes[] = "<label><input type=\"checkbox\" name=\"as_blog\" value=\"1\" tabindex=\"".($cur_index++)."\"".($blog ? ' checked="true"' : '')." />Разместить сообщение в <a href=\"http://www.balancer.ru/user/{$pun_user['id']}/blog/\">Вашем блоге</a>";
$checkboxes[] = "<label><input type=\"checkbox\" name=\"export_blog\" value=\"1\" tabindex=\""
	.($cur_index++).'"'.(isset($_POST['as_blog']) ? ' checked="checked"' : '')
	." onClick=\"getElementById('here_keywords').innerHTML= this.checked ? '"
		.addslashes("<label><strong>Теги:</strong>&nbsp;<input class='longinput' type='text' name='keywords' size='40' maxlength='255' value='"
			.htmlspecialchars(defval($_POST, 'blog_keywords_string', $topic->keywords_string()))
			."' size='40' maxlength='255' /><br /></label>")
	."' : ''\"/>Транслировать ответ в ЖЖ";

if($me && $me->is_coordinator())
	$checkboxes[] = "<label style=\"color:red\"><input type=\"checkbox\" name=\"is_moderatorial\" value=\"1\" tabindex=\"".($cur_index++)."\" ".($post->is_moderatorial()?' checked':'')."/>Данное сообщение - модераториал</label>";

if (!empty($checkboxes))
{

?>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Options'] ?></legend>
					<div class="infldset">
						<div class="rbox">
							<?php echo implode('</label>'."\n\t\t\t\t\t\t\t", $checkboxes).'</label>'."\n" ?>
						</div>
					</div>
				</fieldset>
<?php

	}

?>
			</div>
			<p><input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /><input type="submit" name="preview" value="<?php echo $lang_post['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" /><a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
		</form>
	</div>
</div>
<?php

require 'footer.php';
