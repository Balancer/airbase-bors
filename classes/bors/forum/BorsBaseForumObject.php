<?
	require_once('BorsBaseObject.php');
	class BorsBaseForumObject extends BorsBaseObject
	{
		function template()
		{ 
//			if($this->id() == 20)
				return "xfile://{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/_header.html"; 
//			else
//				return "xfile://{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.new.html"; 
		}
	}
