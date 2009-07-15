<ul>
<?php
foreach($news as $x)
{
	echo "<li><a href=\"{$x->url()}\"";
	if($x->create_time() > time() - 4*86400)
		echo " style=\"color: #c24;\"";
	echo ">".truncate($x->title(), 40)."</a></li>";
}
?>
</ul>
