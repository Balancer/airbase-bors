<?
	require_once('classes/objects/BorsBaseObject.php');
	class borsForumAbstract extends BorsBaseObject
	{
		function template()
		{ 
//			if($this->id() == 20)
				return "xfile://{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/_header.html"; 
//			else
//				return "xfile://{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.new.html"; 
		}
		
    function cache_life_time()
    {
        $GLOBALS['cms']['cache_disabled'] = true;
		return -1;
	}

    function storage_engine() { return 'storage_db_mysql'; }
}
