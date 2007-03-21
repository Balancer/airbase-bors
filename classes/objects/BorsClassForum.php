<?
	require_once('BaseObject.php');
	class BorsClassForum extends BaseObject
	{
		function type() { return 'forum'; }

		function field_title_storage() { return 'punbb.forums.forum_name(id)'; }

		var $stb_parent_forum_id = '';
		function parent_forum_id() { return $this->stb_parent_forum_id; }
		function set_parent_forum_id($parent_forum_id, $db_update = false) { $this->set("parent_forum_id", $parent_forum_id); }
		function field_parent_forum_id_storage() { return 'punbb.forums.parent(id)'; }

		var $stb_category_id = '';
		function category_id() { return $this->stb_category_id; }
		function set_category_id($category_id, $db_update = false) { $this->set("category_id", $category_id); }
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
			include_once("funcs/templates/assign.php");
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
	}
