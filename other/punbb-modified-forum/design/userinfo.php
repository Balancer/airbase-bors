<div class="postleft">
<dl>
	<dt><strong><?php echo $userlink ?></strong></dt>
	<dd class="usertitle"><strong><?php echo $user_title ?></strong></dd>
	<?php if (count($user_info)) echo "\t\t\t\t\t".implode('</dd>'."\n\t\t\t\t\t", $user_info).'</dd>'."\n"; ?>
	<?php if (count($user_contacts)) echo "\t\t\t\t\t".'<dd class="usercontacts">'.implode('&nbsp;&nbsp;', $user_contacts).'</dd>'."\n"; ?>
</dl>
</div>
<div align="right"><?php echo $user_avatar ?></div>


			<div class="postfootleft"><?php if ($cur_post['poster_id'] > 1) echo '<p>'.$is_online.'</p>'; ?></div>
