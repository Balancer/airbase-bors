<div class="blockpost">
<h2>Похожие заголовки форума</h2>
<div class="box" style="padding: 2px;">
<?php
	include("modules/show/forum-titles-like.inc.php");
	echo $ref = show_titles_like($cur_topic['subject'], 20, $cur_topic['forum_id']);
	if(!$ref)
		$GLOBALS['global_cache'] = NULL;
?>
</div>
</div>
