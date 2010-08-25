<?php

class balancer_users_gmap extends bors_page
{
	function title() { return ec('Карта пользователей онлайн'); }
	function nav_name() { return ec('карта пользователей онлайн'); }
	function config_class() { return 'balancer_board_config'; }

	function is_auto_url_mapped_class() { return true; }

	function pre_show()
	{
		require_once('inc/clients/geoip-place.php');
//		$this->tools()->use_ajax();

		template_js_include("http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAbwjPhNY_fqcwnQcDQSl8ZxQq3IJD3RlKOpbovsnuLQcrY_4wsBS3LrzTyWHwjY-vQKzKeJLilBFSsQ&sensor=true");
		template_js_include("/js/tlabel.2.05.js");

		$ll = array();
		foreach(objects_array('bors_access_log', array('user_id>' => 0, 'user_ip<>' => '', 'group' => 'user_id', 'order' => 'COUNT(*)')) as $x)
		{
			list($country_code, $country_name, $city_name, $city_object) = geoip_info($x->user_ip());
			if($city_object && $x->user()->use_avatar())
			{
				$lat = $city_object->latitude + rand(-100, 100)/500;
				$long = $city_object->longitude + rand(-100, 100)/500;

				$code = "var l = new TLabel()
l.id = 'u{$x->user_id()}'
l.anchorLatLng = new GLatLng (".str_replace(',','.',$lat).", ".str_replace(',','.', $long).")
l.anchorPoint = 'center';
l.content = '<a href=\"/user/{$x->user_id()}/\" target=\"_blank\"><img class=\"g\" src=\"/cache/forum/punbb/img/avatars/48x48/{$x->user()->use_avatar()}\" title=\"{$x->user()->title()}, $city_name, $country_name\" /></a>'
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
