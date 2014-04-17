<?php

// Обновляем репутационные коэффициенты для расчёта числа звёздочек.
$top = bors_find_all('balancer_board_user', array('order' => '-reputation', 'limit' => 50));
$max5 = $top[0]->reputation(); // Максимальное значение, 5 полных звёзд. 100%
$max3 = $top[49]->reputation(); // 50-е место — три полных звезды. 60%

// 129 -> 100
// 50 -> 60
// (p-60)/(100-60) = (r-50)/(129-50)
// p = 60+(100-60)*(r-50)/(129-50)

// $percent = 60 + 40*($rep - $rep3) / ($rep5 - $r3)

$min_pos = bors_find_first('balancer_board_user', array('order' => 'reputation', 'reputation>0', 'limit' => 50));
echo "min_pos = {$min_pos->reputation()}; http://balancer.ru/users/".$min_pos->id(),PHP_EOL;
//echo "\$percent = 60 + 40*(\$rep - $max3) / ".($max5 - $max3), PHP_EOL;
echo "\$percent = 100*(\$rep) / $max5;", PHP_EOL;

$low = bors_find_all('balancer_board_user', array('order' => 'reputation', 'limit' => 50));
$min5 = $low[0]->reputation();
$min3 = $low[49]->reputation();

// pos:
// $rep = 200*atan($reputation_value*$reputation_value/300)/pi();
// tan(pi()*$rep/200) = $val*$val/$c
// $c = $val*$val/(tan(pi()*$rep/200))
// 60=200*atan(50**2/2000)/pi
// 60*pi/200 = atan(50**2/2000)
// tan(60*pi/200) = 50**2/2000
// 2000 = 50**2/(tan(60*pi/200))


//$div_neg = $min3*$min3 / (tan(60*pi()/200));
//echo $div_neg, PHP_EOL;

// -26 -> -100
// 0 -> 0
// (p)/(-100) = (r)/(-26)
// p = (-100)*(r)/(-26)

// $percent = -10 - 90*($rep - $rep3) / ($rep5 - $rep3)

//echo "-100*(\$rep) / $min5", PHP_EOL;

$min_neg = bors_find_first('balancer_board_user', array('order' => '-reputation', 'reputation<0', 'limit' => 50));
echo "min_neg = {$min_neg->reputation()}; http://balancer.ru/users/".$min_neg->id(),PHP_EOL;
//echo "\$percent = -10 - 90*(\$rep - $min3) / ".($min5 - $min3), PHP_EOL;
echo "\$percent = -100*(\$rep) / $min5;", PHP_EOL;

// neg:
// $rep = 200*atan($reputation_value*$reputation_value/100)/pi();

