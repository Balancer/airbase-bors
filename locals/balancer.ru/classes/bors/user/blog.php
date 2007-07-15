<?
	class_include('def_dbpage');

	class user_blog extends def_dbpage
	{
		function _class_file() { return __FILE__; }

        function main_db_storage(){ return 'punbb'; }

		function template() { return BORS_INCLUDE.'templates/forum/_header.html'; }

		var $user;
	
		function title() { return $this->user->title().ec(": Блог"); }
		function nav_name() { return ec("блог"); }

		function parents() { return array("forum_user://".$this->id()); }

		function _queries()
		{
			return array('topics' => 'SELECT id FROM topics WHERE poster_id='.$this->id().' ORDER BY posted DESC LIMIT '.(($this->page()-1)*25).', 25');
		}

		function user_blog($id)
		{
			$this->user = class_load('forum_user', $id);
			parent::def_dbpage($id);
			
			$this->add_template_data('user_id', $id);
		}

		function uri($page = 1)
		{	
			if($page < 2)
				return "http://balancer.ru/user/".$this->id()."/blog/"; 
			else
				return "http://balancer.ru/user/".$this->id()."/blog/$page.html"; 
		}

		function cache_static()
		{
			return 86400*30;
		}
		
		function total_pages()
		{
			$blog_per_page = 25;
			return intval(($this->num_blog() - 1 )/ $blog_per_page) + 1;
		}

		function pages_links()
		{
			if($this->total_pages() < 2)
				return "";

			include_once('funcs/design/page_split.php');
			return join(" ", pages_show($this, $this->total_pages(), 20));
		}

		function num_blog() { return $this->db->get('SELECT COUNT(*) FROM topics WHERE poster_id='.$this->id()); }
	}
