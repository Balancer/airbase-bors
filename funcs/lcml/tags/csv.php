<?
    include_once('funcs/lcml/bcsTable.php');

   // Explode CSV string
   function csv_explode($str, $delim = ',', $qual = "\"")
   {
       $skipchars = array( $qual, "\\" );
       $len = strlen($str);
       $inside = false;
       $word = '';
       for ($i = 0; $i < $len; ++$i) 
       {
           $c=substr($str,$i,1);
           if ($c == $delim && !$inside) 
           {
               $out[] = $word;
               $word = '';
           } 
           else if ($inside && in_array($c, $skipchars) && ($i<$len && substr($str,$i+1,1) == $qual)) 
           {
               $word .= $qual;
               $i++;
           } 
           else if ($c == $qual) 
           {
               $inside = !$inside;
           } 
           else {
               $word .= $c;
           }
       }
       $out[] = $word;
       return $out;
   }

    function lp_csv($txt, $params)
    {

        $tab = new bcsTable();

        if(!empty($params['width']))
            $tab->table_width($params['width']);
            
//        if(!empty($params['noborder']))
//            echo $params['noborder'];
            
        foreach(split("\n",$txt) as $s)
        {
            if($s)
            {
                foreach(csv_explode($s,';') as $d)
                {
                    if($d && $d{0} == '*')
                    {
                        $d = substr($d,1);
                        $tab->setHead();
                    }
                    if($d=='')
                        $d = '&nbsp;';
                    $tab->append(lcml($d));
                }
                $tab->new_row();
            }
        }

        return remove_format($tab->get_html());
    }

//    echo lp_csv("*1;2;;0\n5;6;7;8",0);
?>