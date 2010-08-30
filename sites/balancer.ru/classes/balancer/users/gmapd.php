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

		template_js_include("http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAbwjPhNY_fqcwnQcDQSl8ZxQq3IJD3RlKOpbovsnuLQcrY_4wsBS3LrzTyWHwjY-vQKzKeJLilBFSsQ&sensor=true");
		template_js_include("/js/tlabel.2.05.js");

		$ll = array();
		foreach(objects_array('balancer_board_post', array('create_time>' => time()-86400, 'poster_ip<>' => '', 'group' => 'owner_id', 'order' => 'COUNT(*)')) as $x)
		{
			list($country_code, $country_name, $city_name, $city_object) = geoip_info($x->poster_ip());
//			if(debug_is_balancer() && $x->owner_id() == 10000)
//				print_d($x->data);
			if($city_object && ($ava = $x->owner()->use_avatar()))
			{
				$lat = $city_object->latitude + rand(-100, 100)/500;
				$long = $city_object->longitude + rand(-100, 100)/500;

				$code = "var l = new TLabel()
l.id = 'u{$x->owner_id()}'
l.anchorLatLng = new GLatLng (".str_replace(',','.',$lat).", ".str_replace(',','.', $long).")
l.anchorPoint = 'center';
l.content = '<a href=\"/user/{$x->owner_id()}/\" target=\"_blank\"><img class=\"g\" src=\"/cache/forum/punbb/img/avatars/48x48/$ava\" title=\"{$x->author_name()}, $city_name, $country_name\" /></a>'
map.addTLabel(l)
";
//l.percentOpacity = 80;

				$ll[] = $code;
			}
		}

		template_js("
function initialize() {

      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById('map_canvas'));
        map.setUIToDefault();

		map.setCenter(new GLatLng(55, 37), 3);
		map.setMapType(G_HYBRID_MAP);

		".join("\n", $ll)."

      }
    }

	if(window.addEventListener)
		window.addEventListener('load',initialize,false); //W3C
	else if(document.addEventListener)
		document.addEventListener('load',initialize,false); //W3C
    else
		document.attachEvent('onload',initialize); //IE

		");


		return parent::pre_show();
	}
}