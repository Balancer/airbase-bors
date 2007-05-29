<?
	require_once('BorsBaseObject.php');
	class Airbaseuser extends BorsBaseObject
	{
		function type() { return 'user'; }

		function field_title_storage() { return 'punbb.categories.cat_name(id)'; }

        function body()
		{
			return ec("Юзер");
		}
	}
