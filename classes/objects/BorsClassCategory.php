<?
	require_once('BorsBaseObject.php');
	class BorsClassCategory extends BorsBaseObject
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
