<?
	require_once('BorsBaseObject.php');
	class BorsBaseForumObject extends BorsBaseObject
	{
		function template() { return "xfile://{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html"; }
	}
