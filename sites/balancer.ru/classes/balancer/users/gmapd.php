<?php

class balancer_users_gmapd extends bors_page
{
	function title() { return ec('Карта авторов сообщений за сутки'); }
	function nav_name() { return ec('карта авторов сообщений за сутки'); }
	function config_class() { return 'balancer_board_config'; }

	function is_auto_url_mapped_class() { return true; }

	function pre_show()
	{
		require_once('inc/clients/geoip-place.php');
//		$this->tools()->use_ajax();

		template_js_include("//maps.googleapis.com/maps/api/js?v=3.exp");
//		template_js_include("/js/tlabel.2.05.js");

		$ll = array();
		foreach(bors_find_all('balancer_board_post', [
			'*set' => 'COUNT(*) as `count`',
			'create_time>' => time()-86400,
			'poster_ip<>' => '',
			'group' => 'owner_id',
			'order' => 'COUNT(*)']
		) as $x)
		{
			list($country_code, $country_name, $city_name, $city_object) = geoip_info($x->poster_ip());
			if($city_object && ($ava = $x->owner()->use_avatar()))
			{
				$lat = $city_object->latitude + rand(-100, 100)/500;
				$long = $city_object->longitude + rand(-100, 100)/500;

				$code = "{img:'/cache/forum/punbb/img/avatars/48x48/{$ava}',"
					.'lat:'.str_replace(',','.',$lat).',long:'.str_replace(',','.', $long).','
					."url:'/user/{$x->owner_id()}/',"
					."title:'".htmlspecialchars($x->owner()->title().", $city_name, $country_name")."',"
					."count: ".intval($x->count()).'}';

				$ll[] = $code;
			}
		}

		template_js("
var users = [\n".join(",\n", $ll)."]

function initialize() {
	var mapOptions = {
		zoom: 3,
		center: new google.maps.LatLng(55, 37)
	}
	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions)
	setUsers(map, users)
}

function setUsers(map, users) {
	var shape = {
		coords: [
			1, 1,
			1, 48,
			48, 48,
			48 , 1],
		type: 'poly'
	};

	for (var i = 0; i < users.length; i++) {
		u = users[i]
		var image = u['img']
	    var myLatLng = new google.maps.LatLng(u['lat'], u['long']);
    	var marker = new google.maps.Marker({
	        position: myLatLng,
    	    map: map,
        	icon: image,
//    	    shape: shape,
			title: u['title'],
			zIndex: u['count']
    });
  }
}

google.maps.event.addDomListener(window, 'load', initialize);

		");

		return parent::pre_show();
	}
}
