<?
class user_posts extends base_page_db
{
	function _class_file() { return __FILE__; }

	function main_db_storage(){ return 'punbb'; }

	function template()
	{
		templates_noindex();
		return 'forum/_header.html';
	}	

		var $user;
	
		function title() { return $this->user->title().ec(": Все сообщения"); }
		function nav_name() { return ec("все сообщения"); }

		function parents() { return array("forum_user://".$this->id()); }

		function _queries()
		{
			return array('posts' => 'SELECT id FROM posts WHERE poster_id='.$this->id().' ORDER BY posted LIMIT '.(($this->page()-1)*25).', 25');
		}

		function __construct($id)
		{
			$this->user = class_load('forum_user', $id);
			parent::__construct($id);
			
			$this->add_template_data('user_id', $id);
		}

		function url($page = 1)
		{	
			if($page < 2)
				return "http://balancer.ru/user/".$this->id()."/posts/"; 
			else
				return "http://balancer.ru/user/".$this->id()."/posts/$page.html"; 
		}

		function cache_static()
		{
			return 86400*30;
		}
		
		function total_pages()
		{
			$posts_per_page = 25;
			return intval(($this->num_posts() - 1 )/ $posts_per_page) + 1;
		}

		function pages_links()
		{
			if($this->total_pages() < 2)
				return "";

			include_once('funcs/design/page_split.php');
			return join(" ", pages_show($this, $this->total_pages(), 18));
		}

		function num_posts() { return $this->db->get('SELECT COUNT(*) FROM posts WHERE poster_id='.$this->id()); }

	function can_be_empty() { return true; }
}
