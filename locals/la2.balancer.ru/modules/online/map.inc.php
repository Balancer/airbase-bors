<?
	function lbr_module_online_map($db_name = 'l2jdb')
	{
		$GLOBALS['cms']['cache_disabled'] = false;

		$cache = new Cache();
		
		if($cache->get('LBR:DB',"$db_name-online-v5"))
			return $cache->last();

		$map = "";

		$MAP_MIN_X = -131072;
		$MAP_MAX_X = 228608;
		$MAP_MIN_Y = -256144;
		$MAP_MAX_Y = 262144;
		
		$db = new DataBase($db_name);
		
		list($map_img_w, $map_img_h) = getimagesize("{$_SERVER['DOCUMENT_ROOT']}/images/maps/mediumMap.jpg");
		
		foreach($db->get_array("SELECT char_name, x, y FROM characters WHERE online > 0") as $row)
		{
			$m_x = intval($map_img_w*($row['x']-$MAP_MIN_X)/($MAP_MAX_X-$MAP_MIN_X)-3);
			$m_y = intval($map_img_h*($row['y']-$MAP_MIN_Y)/($MAP_MAX_Y-$MAP_MIN_Y)-3);

			$map .=	"<div style=\"position:absolute;top:{$m_y}px;left:{$m_x}px\"><img src=\"/i/monster.gif\"></div>"; //  title=\"{$row['char_name']}\"
		}

$out = <<< __EOT__
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" width="461">
  <tr width="{$map_img_w}" height="1"><td colSpan="2" width="{$map_img_w}" height="1"><img src="/i/tdot.gif" width="{$map_img_w}" height="1"></td></tr>
  <tr width="{$map_img_w}" height="{$map_img_h}">
	<td valign="top" height="{$map_img_h}" width="1"><img src="/i/tdot.gif" width="1" height="{map_img_h}"></td>
    <td valign="top" height="{$map_img_h}" width="{$map_img_w}">
      <div style="position:absolute">
        <img src="http://la2.balancer.ru/images/maps/mediumMap.jpg" width="{$map_img_w}" height="{$map_img_h}" border="1" style="border-color:#000000;" style="position:absolute;top:0px;left:0px;"/>
		$map
      </div>
    </td>
  </tr>
</table>
</div>
__EOT__;
	
		return $cache->set($out, 300);
	}
	