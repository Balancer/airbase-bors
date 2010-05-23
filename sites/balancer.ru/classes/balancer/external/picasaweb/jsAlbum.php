<?php

class balancer_external_picasaweb_jsAlbum extends base_js
{
	function album_data()
	{
		$ch = new Cache();
		if($ch->get('picasaweb-album-data', $this->id()))
			return $ch->last();

		require_once('inc/http.php');
		require_once('/usr/share/webapps/phpldapadmin/1.2.0.4/htdocs/lib/xml2array.php');
		list($name, $id) = explode('/', $this->id());
		$album_url = "http://picasaweb.google.com/data/feed/api/user/{$name}/album/{$id}";
		$s = http_get($album_url);
		$x = new xml2array();
		$data = $x->parseXML($s, NULL);
//		print_d($data);

		for($e = 0; $e < count($data['feed']['entry']); $e++)
			$data['feed']['entry'][$e]['thumb'] = preg_replace(
				"!^(.+/)([^/]+)$!",
				"$1s640/$2",
				$data['feed']['entry'][$e]['content']['SRC']
			);

		return $ch->set($data, 600);
	}

	function local_data()
	{
		return array(
			'data' => $this->album_data(),
		);
	}
}
