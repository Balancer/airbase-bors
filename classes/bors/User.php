<?
	require_once('classes/objects/BorsBaseObject.php');
	class BorsClassUser extends BorsBaseObject
	{
		function class_name() { return 'User'; }

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

			$this->_title = $this->rank();

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

		var $stb_num_posts;
		function num_posts() { return $this->stb_num_posts; }
		function set_num_posts($num_posts, $db_update = false) { $this->set("num_posts", $num_posts, $db_update); }
		function field_num_posts_storage() { return 'punbb.users.num_posts(id)'; }

		function rank()
		{
			$db = &new DataBase('punbb');
			return $db->get("SELECT rank FROM ranks WHERE min_posts < ".intval($this->num_posts())." ORDER BY min_posts DESC LIMIT 1");
		}

		var $stb_signature;
		function signature() { return $this->stb_signature; }
		function set_signature($signature, $db_update = false) { $this->set("signature", $signature, $db_update); }
		function field_signature_storage() { return 'punbb.users.signature(id)'; }

		var $stb_signature_html;
		function signature_html()
		{
			if(empty($this->stb_signature_html) || !empty($GLOBALS['bors_data']['lcml_cache_disabled']))
			{
				$body = lcml($this->signature(), 
					array(
						'cr_type' => 'save_cr',
						'forum_type' => 'punbb',
						'forum_base_uri' => 'http://balancer.ru/forum',
						'sharp_not_comment' => true,
						'html_disable' => true,
					)
				);
	
				$this->set_signature_html($body, true);
			}				
				
			return $this->stb_signature_html; 
		}

		function set_signature_html($signature_html, $db_update = false) { $this->set("signature_html", $signature_html, $db_update); }
		function field_signature_html_storage() { return 'punbb.users.signature_html(id)'; }
	}
