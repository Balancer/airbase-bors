<?
	require_once('classes/bors/users/borsUsersAbstract.php');
	require_once('classes/bors/borsUser.php');
	
	class borsUsersUseTopics extends borsUsersAbstract
	{
		function _class_file() { return __FILE__; }
		
		var $user;
		var $db;
		
		function borsUsersUseTopics($id)
		{
			parent::borsUsersAbstract($id);
			$this->user = new borsUser($id);
			$db = new DataBase('punbb');
			$this->add_template_data('topics', $db->get_array("SELECT DISTINCT topic_id FROM posts WHERE poster_id = ".intval($this->id())." AND posted > ".(time()-30*86400)." ORDER BY posted DESC LIMIT 100", false, 600));
			$this->add_template_data('skip_subforums', true);
		}

		function url()
		{
			return "http://balancer.ru/user/".$this->id()."/use-topics.html";
		}
	
		function title()
		{
			return $this->user->title().ec(": темы с участием");
		}
	}
