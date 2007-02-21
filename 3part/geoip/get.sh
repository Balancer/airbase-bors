#!/bin/bash

for i in README geoip.inc    geoipcity.inc  geoipregionvars.php   sample.php  sample_city.php      ; do
	wget -nH -nd -r -L 1 http://www.maxmind.com/download/geoip/api/php/$i
done


