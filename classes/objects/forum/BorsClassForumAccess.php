<?
	require_once('classes/objects/BorsBaseObject.php');
	class BorsClassForumAccess extends BorsBaseObject
	{
		var $forum_id = '';
		var $group_id = '';

		function BorsClassForumAccess($id)
		{
//			echo "BorsClassForumAccess($id)<br />";
			list($this->forum_id, $this->group_id) = split(':', $id);
			$this->BorsBaseObject($id);
		}

		var $stb_can_read;
		function can_read()
		{
			if($this->stb_can_read !== NULL)
				return $this->stb_can_read;
			
			return class_load('group', $this->group_id)->can_read();
		}
		
		function set_can_read($can_read, $db_update = false) { $this->set("can_read", $can_read, $db_update); }
		function field_can_read_storage() { return "punbb.forum_perms.read_forum(group_id=".intval($this->group_id)." AND forum_id=".intval($this->forum_id).")"; }

		var $stb_can_post;
		function can_post() { return $this->stb_can_post; }
		function set_can_post($can_post, $db_update = false) { $this->set("can_post", $can_post, $db_update); }
		function field_can_post_storage() { return "punbb.forum_perms.post_replies(group_id=".intval($this->group_id)." AND forum_id=".intval($this->forum_id).")"; }

		var $stb_can_new;
		function can_new() { return $this->stb_can_new; }
		function set_can_new($can_new, $db_update = false) { $this->set("can_new", $can_new, $db_update); }
		function field_can_new_storage() { return "punbb.forum_perms.post_topics(group_id=".intval($this->group_id)." AND forum_id=".intval($this->forum_id).")"; }
	}
