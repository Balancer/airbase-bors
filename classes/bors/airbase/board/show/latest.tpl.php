<table class="btab">
<?php

foreach($topics as $t)
{
	echo "<tr><td><a href=\"{$t->url()}\">{$t->title()}</a></td>";
	echo "<td>{$t->forum()->titled_url()}</td>";
//	echo "<td><a href=\"http://balancer.ru/forum/punbb/viewforum.php?id={$t->forum_id()}\">{$t->forum_title()}</a></td>";
	echo "<td>{$t->last_poster_name()}</a>, ".short_time($t->modify_time())."</td>";
	echo "</tr>";
}
?>
</table>
