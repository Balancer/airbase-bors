<?
	require_once('BorsBaseObject.php');
	class BorsClassForum extends BorsBaseObject
	{
		function type() { return 'forum'; }

		function field_title_storage() { return 'punbb.forums.forum_name(id)'; }

		var $stb_parent_forum_id = '';
		function parent_forum_id() { return $this->stb_parent_forum_id; }
		function set_parent_forum_id($parent_forum_id, $db_update = false) { $this->set("parent_forum_id", $parent_forum_id, $db_update); }
		function field_parent_forum_id_storage() { return 'punbb.forums.parent(id)'; }

		var $stb_category_id = '';
		function category_id() { return $this->stb_category_id; }
		function set_category_id($category_id, $db_update = false) { $this->set("category_id", $category_id, $db_update); }
		function field_category_id_storage() { return 'punbb.forums.cat_id(id)'; }

        function parents()
		{
			if($this->parent_forum_id())
				return array(array('forum', $this->parent_forum_id() ));
			else
				return array(array('category', $this->category_id() ));
		}

        function body()
		{
			$this->cache_clean_self();
		
			global $bors;

			
			if(!$this->can_read())
				return ec("Извините, доступ к этому ресурсу закрыт для Вас");

			if($this->is_public_access())
				$GLOBALS['cms']['cache_static'] = true;

			include_once("funcs/templates/assign.php");

			$bors->config()->set_cache_uri($this->internal_uri());
			
			$data = array();

			$db = &new DataBase('punbb');

			$topics_per_page = 50;
			$start_from = ($this->page() - 1) * $topics_per_page;

			$topics = $db->get_array("SELECT id FROM topics WHERE forum_id={$this->id()} ORDER BY last_post DESC LIMIT $start_from, $topics_per_page");

			$data['topics'] = array();

			foreach($topics as $tid)
				$data['topics'][] = class_load('topic', $tid);

			return template_assign_data("BorsClassForumBody.html", $data);
		}

		function is_public_access()
		{
			$access = class_load('forum/forumAccess', "{$this->id()}:3");
			return $access->can_read();
		}

		function can_read()
		{
			$access = class_load('forum/forumAccess', "{$this->id()}:" . class_load('user', -1)->group_id());
			return $access->can_read();
		}

		function cache_parents()
		{ 
			$parent_caches = array();
			if($this->parent_forum_id())
				$parent_caches[] = class_load('forum', $this->parent_forum_id());

			if($this->category_id())
				$parent_caches[] = class_load('category', $this->category_id());
			
			return $parent_caches;
		}
	}
