<html>
<head><title>{$title}</title></head>
<body>
<small>{call func=module param="top-navs.php"}</small>
<div align="right">{$description}</div>
<h1>{$title}</h1>
{$body}

{if $access}<hr /><small><center> edit <a href="?edit">page</a> | <a href="?edit-data">data</a></center></small><hr />{/if}
</body>
</html>
