<?
	require_once('BaseObject.php');
	class AP_User extends BaseObject
	{
		var $stb_email = '';
		
		function email() { return $this->stb_email; }
		function set_email($new_email) { $this->set("email", $new_email); }
		function field_email_storage()
		{
			return 'WWW.UserPrefs.EMail(LoginID)';
		}
	}