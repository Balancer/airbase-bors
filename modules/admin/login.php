<?
    ini_set('include_path', ini_get('include_path') . ":/www/docs/www1001kran/www/cms");

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');

    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");
    require_once("funcs/users.php");
    require_once("funcs/navigation/go.php");
    require_once("actions/recompile.php");

    function data_clear()
    {
		SetCookie("login","",0,"/");
		SetCookie("password","",0,"/");
		$_COOKIE['login'] = NULL;
		$_COOKIE['password'] = NULL;
		$_POST['login'] = NULL;
		$_POST['password'] = NULL;
    }

    function do_login()
    {
		if(!empty($_POST))
		{
			if(!empty($_POST['logout']))
			{
				data_clear();
				echo "<b>Logout successful</b><br><br>";
			}
			else
			{
				SetCookie("login",$_POST['login'],time()+2592000,"/");
				SetCookie("password",$_POST['password'],time()+2592000,"/");
				$_COOKIE['login'] = $_POST['login'];
				$_COOKIE['password'] = $_POST['password'];
				if(!user_data("password"))
				{
					echo "<b><font color=\"red\">Unknown user {$_POST['login']}!</font></b><br><br>";
					data_clear();
				}
			   	else if(user_data("password") == $_POST['password'])
			   	{
					echo "<b>Login successful!<br><br></b>";
			  	}
			  	else
			  	{
					echo "<b><font color=\"red\">Password not match for user {$_POST['login']}!</red></b>";
					data_clear();
			  	}
		  	}
		}
		
		if(!empty($_COOKIE['login']))
		{
			echo "<b>Logged as {$_COOKIE['login']} (Access level = ".user_data('level').")";
?>
<form name="logout_form" method="POST">
<table cellSpacing="0" class="btab">
<tr><td><input type="submit" name="logout" value="Logout"></td></tr>
</table>
</form>
<?
			return;
		}
?>
<form name="login_form" method="POST">
<table cellSpacing="0" class="btab">
<tr><td>Login:</td><td><input name="login"></td></tr>
<tr><td>Password:</td><td><input name="password" type="password"></td></tr>
<tr><td colSpan=2><input type="submit" value="Enter"></td></tr>
</table>
</form>
<?
	}

	do_login();
?>