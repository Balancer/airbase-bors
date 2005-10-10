<?
    include_once('funcs/lcml/bcsTable.php');

    function lcml_ascii_tables($txt)
    {
        $txt = preg_replace("!(\+=+\+)(.+?)(\1)!es", "_lcml_ascii_table('$2',1)" ,$txt);
        $txt = preg_replace("!(\+\-+\+)(.+?)(\1)!es", "_lcml_ascii_table('$2',0)" ,$txt);

        return $txt;
    }

    function _lcml_ascii_table($table, $border)
    {
        $tab = new bcsTable();

        $table = preg_replace("!^\n+!s", "", $table);
        $table = preg_replace("!\n+$!s", "", $table);
    
        $rows_data=split("\n", $table);
        $rows=sizeof($rows);
        $cols=strlen($rows[0]);
        $table=array();
        $map=array();
        $cols_map=array();
        $rows_map=array();

        for($j=0; $j<$rows; $j++)
        {
            for($i=0; $i<$cols; $i++)
            {
                $map[$i][$j]=0;
                $table[$i][$j]=substr($rows[$j],$i,1);
            }
        }

        $real_cols=1;
        for($col=0; $col<$cols; $col++)
        {
            $cols_map[$col]=0;
            for($row=0; $row<$rows; $row++)
            {
                if($table[$col][$row]=='|')
                {
                    if(!$cols_map[$col])
                         $real_cols++;
                    $cols_map[$col]=1;
                }
            }
        }

        $real_rows=1;
        for($row=0; $row<$rows; $row++)
        {
            if(!preg_match("!\-!",$rows[$row]))
            {
                $rows_map[$row]=0;
                next;
            }

            if(preg_match("!^[\|\-\+]+$", $rows[$row]))
            {
                $rows_map[$row]=1;
                $real_rows++;
            }
        }

//    die "Таблица $real_cols x $real_rows\n";
/*

    |123|45545|45325235|
    |---------| 1212   |
    |121232313|        |
    |---------|--------|
    |  sdcsdc |  12|2  |

*/
        $cell_number = 1;

        for($row=0; $row<$rows; $row++)
        {
            $
            for($col=1; $col<$cols-1; $col++)
            {
                if(!$rows_map[$row])
                {
                    if(!$cols_map[$col])
                    {
                        // текущая ячейка, ничего не меняется
                        $map[$row][$col] = $cell_number;
                    }
                }
            }
        }

        
}

?>
