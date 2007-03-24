<?
	require_once('BorsBaseForumObject.php');
	class BorsClassForumCategory extends BorsBaseForumObject
	{
		function type() { return 'forumCategory'; }

		function field_title_storage() { return 'punbb.categories.cat_name(id)'; }

		var $stb_parent_category_id = '';
		function parent_category_id() { return $this->stb_parent_category_id; }
		function set_parent_category_id($parent_category_id, $db_update = false) { $this->set("parent_category_id", $parent_category_id, $db_update); }
		function field_parent_category_id_storage() { return 'punbb.categories.parent(id)'; }

		var $stb_base_uri = '';
		function base_uri() { return $this->stb_base_uri; }
		function set_base_uri($base_uri, $db_update = false) { $this->set("base_uri", $base_uri, $db_update); }
		function field_base_uri_storage() { return 'punbb.categories.base_uri(id)'; }

		function parents()
		{
//			echo "Get parents for cat ".$this->id();
			if($this->parent_category_id())
				return array(array('forumCategory', $this->parent_category_id()));

			return array(array('page', 'http://balancer.ru/'));
		}

        function body()
		{
			return ec("Категория");
		}
	}
