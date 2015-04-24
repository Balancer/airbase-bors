<?php

class balancer_external_picasaweb_bbAlbum extends bors_page
{
	function album_data()
	{
		$ch = new Cache();
		if($ch->get('picasaweb-album-data-v3', $this->id()))
			return $ch->last();

		require_once('inc/http.php');
		require_once('/usr/share/webapps/phpldapadmin/1.2.0.4/htdocs/lib/xml2array.php');
		list($name, $id) = explode('/', $this->id());
		$album_url = "http://picasaweb.google.com/data/feed/api/user/{$name}/album/{$id}";
		$s = http_get($album_url);
		$x = new xml2array();
		$data = $x->parseXML($s, NULL);

		for($e = 0; $e < count($data['feed']['entry']); $e++)
		{
//			var_dump($data['feed']['entry'][$e]);
			$data['feed']['entry'][$e]['thumb'] = preg_replace(
				"!^(.+/)([^/]+)$!",
				"$1s640/$2",
				$data['feed']['entry'][$e]['content']['SRC']
			);

			if(!is_array($data['feed']['entry'][$e]['media:group']['media:description']))
				$data['feed']['entry'][$e]['desc'] = $data['feed']['entry'][$e]['media:group']['media:description'];
		}

		return $ch->set($data, 600);
	}

	function body_data()
	{
		return array(
			'data' => $this->album_data(),
		);
	}
}
