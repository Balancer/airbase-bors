<?
	require_once('BaseObject.php');
	class BorsClassCategory extends BaseObject
	{
		function type() { return 'category'; }

		function field_title_storage() { return 'punbb.categories.cat_name(id)'; }

        function parents()
		{
			return array();
		}

        function body()
		{
			return ec("Категория");
		}
	}
