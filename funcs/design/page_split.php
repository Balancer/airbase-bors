<?
	function pages_select($page, $current_page, $total_pages)
	{
		$pages = array();
		$total_pages = intval($total_pages);

		$q = "";
		if(!empty($_GET))
			foreach($_GET as $key => $value)
				$q .= ($q=="") ? "?$key=$value" : "&$key=$value";
		
		if($total_pages > 1)
		{
			$last = 0;
			for($i=1; $i <= $total_pages; $i++)
			{
				if(!check_page($i, $current_page, $total_pages))
					continue;
					
				if($last != $i-1)
					$pages[]=' ... ';
				
				$last = $i;
				
				if(is_object($page))
					$p = $page->uri($i);
				else
				{
					$p = $page;
					if($i > 1)
						$p .= "page$i/";
				}
				
				$pages[] = "<a href=\"$p$q\" class=\"".(($i==$current_page)?'current_page':'select_page')."\">$i</a>";
			}
		}
		
		return $pages;
	}

	function pages_start_stop_calculate($current_page, $total_pages, $limit)
	{
		if($total_pages <= $limit) // 1, 2, 3
			return array(2, $total_pages - 1);

		if($current_page > 0)
			$min = min($current_page - intval($limit/2), $total_pages - $limit);
		else
			$min = $total_pages - $limit + 2;
			
		$min = max(2, $min);
		
		if($min > 2)
		{
//			$limit--;
			$min++;
		}
		
		if($min == 2)
			$max = max($limit - 2, $min + $limit - 2);
		else
			$max = max($limit - 2, $min + $limit - 3);
			
		if($max >= $total_pages)
			$max = $total_pages - 1;
		
		return array($min, $max);
	}

	function pages_show($obj, $total_pages, $limit, $show_current = true, $current_page_class = 'current_page', $other_page_class = 'select_page')
	{

		$pages = array();
		$total_pages = intval($total_pages);
		$current_page = $show_current ? $obj->page() : -1;

		if($total_pages < 2)
			return $pages;

		$q = "";
		if(!empty($_GET))
			foreach($_GET as $key => $value)
				$q .= ($q=="") ? "?$key=$value" : "&$key=$value";

		list($start, $stop) = pages_start_stop_calculate($current_page, $total_pages, $limit);
//		$pages[] = $start;
//		$pages[] = $stop;
		
		$pages[] = get_page_link($obj, 1, 1==$current_page ? $current_page_class : $other_page_class, $q);
		
		if($start > 2)
			$pages[] = "...";

		for($i = $start; $i <= $stop; $i++)
			$pages[] = get_page_link($obj, $i, $i==$current_page ? $current_page_class : $other_page_class, $q);
		
		if($stop < $total_pages - 2)
			$pages[] = "...";

		$pages[] = get_page_link($obj, $total_pages, $total_pages==$current_page ? $current_page_class : $other_page_class, $q);
		
//		for($i = $total_pages - intval($limit/2) + 1; $i <= $total_pages; $i++)
//			$pages[] = get_page_link($obj, $i, $i==$current_page ? $current_page_class : $other_page_class, $q);
		
//		print_r($pages);
		
		return $pages;
	}

	function get_page_link($obj, $page_num, $class="", $q = "")
	{
		if(is_object($obj))
			$p = $obj->uri($page_num);
		else
		{
			$p = $obj;
			if($page_num > 1)
				$p .= "page$page_num/";
		}
				
		return "<a href=\"$p$q\"".($class? " class=\"$class\"" : "" ).">$page_num</a>";
	}

	function check_page($p, $current_page, $total_pages)
	{
		if($p < 3)					return true;
		if($p > $total_pages - 2)	return true;
		if(abs($p - $current_page) <= 5)	return true;
		if($p == $current_page-6 && $p == 3) return true;
		if($p <= 14 && $current_page < 10) return true;
	
		if($p == $current_page+6 && $p == $total_pages-2) return true;
		if($p >= $total_pages-13 && $current_page > $total_pages-9) return true;

		return false;
	}
