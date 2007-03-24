<?
	require_once('BorsBaseObject.php');
	class BorsClassUser extends BorsBaseObject
	{
		function type() { return 'user'; }

		function BorsClassUser($id)
		{
//			echo "BorsClassUser($id)<br />";
			if($id == -1)
			{
				global $me;
				if(empty($me) || !is_object($me))
					$me = &new User();
				$id = $me->get('id');
				
//				echo "Current user id = $id<br />";
			}
			
			$this->BorsBaseObject($id);
		}


		function field_title_storage() { return 'punbb.users.username(id)'; }

        function body() { return ec("Пользователь '{$this->title()}' (№{$this->id()})"); }

		var $stb_group_id;
		function group_id() { return $this->stb_group_id; }
		function set_group_id($group_id, $db_update = false) { $this->set("group_id", $group_id, $db_update); }
		function field_group_id_storage() { return 'punbb.users.group_id(id)'; }


		function group() { return class_load('group', $this->group_id()); }

		var $stb_user_title;
		function user_title() { return $this->stb_user_title; }
		function set_user_title($user_title, $db_update = false) { $this->set("user_title", $user_title, $db_update); }
		function field_user_title_storage() { return 'punbb.users.title(id)'; }

		var $_title = NULL;
		function group_title()
		{
			if($this->_title)
				return $this->_title;
			
			if($this->_title = $this->user_title())
				return $this->_title;
				
			if($this->_title = $this->group()->user_title())
				return $this->_title;

			return $this->_title;
		}

		var $stb_use_avatar;
		function use_avatar() { return $this->stb_use_avatar; }
		function set_use_avatar($use_avatar, $db_update = false) { $this->set("use_avatar", $use_avatar, $db_update); }
		function field_use_avatar_storage() { return 'punbb.users.use_avatar(id)'; }

		var $stb_avatar_width;
		function avatar_width() { return $this->stb_avatar_width; }
		function set_avatar_width($avatar_width, $db_update = false) { $this->set("avatar_width", $avatar_width, $db_update); }
		function field_avatar_width_storage() { return 'punbb.users.avatar_width(id)'; }

		var $stb_avatar_height;
		function avatar_height() { return $this->stb_avatar_height; }
		function set_avatar_height($avatar_height, $db_update = false) { $this->set("avatar_height", $avatar_height, $db_update); }
		function field_avatar_height_storage() { return 'punbb.users.avatar_height(id)'; }
	}
