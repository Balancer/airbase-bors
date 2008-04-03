<?
	class forum_category extends forum_abstract
	{
		function field_title_storage() { return 'punbb.categories.cat_name(id)'; }
	function storage_engine() { return 'storage_db_mysql'; }

		function uri_name() { return 'category'; }

		var $stb_parent_category_id = '';
		function parent_category_id() { return $this->stb_parent_category_id; }
		function set_parent_category_id($parent_category_id, $db_update) { $this->set("parent_category_id", $parent_category_id, $db_update); }
		function field_parent_category_id_storage() { return 'punbb.categories.parent(id)'; }

		var $stb_category_base = '';
		function category_base() { return $this->stb_category_base; }
		function set_category_base($category_base, $db_update) { $this->set("category_base", $category_base, $db_update); }
		function field_category_base_storage() { return 'punbb.categories.base_uri(id)'; }

		function url() { return $this->category_base_full(); }
	
		function category_base_full()
		{
			$cat = $this;
			while(!$cat->category_base() && $this->parent_category_id())
				$cat = class_load('forum_category', $this->parent_category_id());
			
			return $cat->category_base();
		}

		function parents()
		{
//			echo "Get parents for cat ".$this->id();
			if($this->parent_category_id())
				return array("forum_category://". $this->parent_category_id());

			return array("http://balancer.ru/forum/");
		}

		function direct_subcats_ids()
		{
			// Получаем одни cat_id для дочерних категорий первого уровня
			$db = &new DataBase('punbb');
			
			return $db->get_array("SELECT id FROM categories WHERE parent = {$this->id()}");
		}

		function direct_subcats()
		{
			$subcats = array();
			foreach($this->direct_subcats_ids() as $cat_id)
				$subcats[] = class_load('forum_category', $cat_id);
			return $subcats;
		}
		
		function all_subcats(&$processed = array())
		{
			$cats = array();
			
			foreach($this->direct_subforums_ids() as $forum_id)
			{
				if(in_array($forum_id, $processed))
					continue;

				$processed[] = $forum_id;
				$subforum = $cats[] = class_load('forum_category', $forum_id);
				$cats = array_merge($cats, $subforum->all_subforums(&$processed));
			}
			
			return $cats;
		}

		function direct_subforums_ids()
		{
			// Получаем одни forum_id для дочерних форумов первого уровня
			$db = &new DataBase('punbb');
			
			return $db->get_array("SELECT id FROM forums WHERE cat_id = {$this->id()}");
		}

		function direct_subforums()
		{
			$subforums = array();
			foreach($this->direct_subforums_ids() as $forum_id)
				$subforums[] = class_load('forum_forum', $forum_id);
			return $subforums;
		}

/*		function all_subforums(&$cats_processed = array(), &$forums_processed = array())
		{
			$forums = array();
			
			foreach(array_merge($this->id(), $this->direct_subcats_ids()) as $cat_id)
			{
				if(in_array($cat_id, $cats_processed))
					continue;
				
				$cats_processed[] = $cat_id;
				$subcat = class_load('forumCategory', $cat_id);
				
				foreach($subcat->direct_subforums_ids() as $forum_id)
				{
					if(in_array($forum_id, $forums_processed))
						continue;

					$forumms_processed[] = $forum_id;
					$subforum = $forums[] = class_load('forum', $forum_id);
					$forums = array_merge($forums, $subforum->all_subforums(&$cats_processed, &$forums_processed));
				}
			}
					
			return $forums;
		}
*/

}
