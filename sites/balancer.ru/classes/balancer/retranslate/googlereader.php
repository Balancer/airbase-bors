<?php

class balancer_retranslate_googlereader extends base_page
{
	function render_engine() { return __CLASS__; }
	function is_auto_url_mapped_class() { return true; }

	function render()
	{
		// ispired from http://www.mezzoblue.com/archives/2008/12/11/authenticati/

		// ----------------------------------------
		// Google Reader Authentication in PHP
		// a basic script to get you in the door of 
		// Google's unofficial Reader API
		// by Dave Shea, mezzoblue.com
		// ----------------------------------------

		// cobbled together from notes on:
		// http://code.google.com/p/pyrfeed/wiki/GoogleReaderAPI

		// these are the urls we'll need to access various services
		$urlAtom = "http://www.google.com/reader/atom";

		$ch = curl_init('https://www.google.com/accounts/ClientLogin');

		$data = array('accountType' => 'GOOGLE',
			'Email' => config('balabot.login.google'),
			'Passwd' => config('balabot.password.google'),
			'source'=>'BalaBot retranslator',
			'service'=>'reader',
		);

		curl_setopt_array($ch, array(
			CURLOPT_TIMEOUT => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 3,
			CURLOPT_AUTOREFERER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data,
		));

		$loginResult = curl_exec($ch);
		curl_close ($ch);

		var_dump($loginResult);
		exit();

		// we just received three lines of ugliness to contend with.
		// each line is a huge string preceded with an ID
		// the IDs are: SID, LSID, and Auth; we only want SID
		// let's use some string parsing to weed it out
		if ($i = strstr($loginResult, "LSID"))
		{
			$SID = substr($loginResult, 0, (strlen($loginResult) - strlen($i)));
			$SID = rtrim(substr($SID, 4, (strlen($SID) - 4)));
		}

		// so we've found the SID
		// now we can build the cookie that gets us in the door
		$cookie = "SID=" . $SID . "; domain=.google.com; path=/; expires=1600000000";

		// this builds the action we'd like the API to perform
		// in this case, it's getting our list of unread items
//		$action = $urlAtom . "/user/-/state/com.google/reading-list";
		$action = $urlAtom . "/user/-/state/com.google/starred";
		// note that the hyphen above is a shortcut
		// for "the currently logged-in user"

		// start buffering what we get back
		ob_start();
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $action);
		curl_setopt ($ch, CURLOPT_HTTPGET, true);
		curl_setopt ($ch, CURLOPT_COOKIE, $cookie);
		curl_exec ($ch);
		curl_close ($ch);
		// throw the buffer into a variable
		$xml = ob_get_contents();
		ob_end_clean();

		// and finally, let's take a look.
//		header("Content-Type: ".$feed->contentType."; charset=".$feed->encoding);
		echo $xml;
	}
}
