<?
	require_once('classes/objects/BorsBaseObject.php');
	class BorsClassForumAccess extends BorsBaseObject
	{
		var $forum_id = '';
		var $group_id = '';

		function BorsClassForumAccess($id)
		{
			list($this->forum_id, $this->group_id) = split(':', $id);

			parent::BorsBaseObject($id);
		}

		var $stb_access = '';
		function access() { return $this->stb_access; }
//		function set_access($access, $db_update = false) { $this->set("access", $access, $db_update); }
		function field_access_storage() { return "punbb.forums.parent(group_id=".intval($this->group_id)." AND id=".intval($this->forum_id).")"; }
	}
