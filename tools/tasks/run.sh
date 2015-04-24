#!/bin/bash

export BORS_SITE=/var/www/bors/bors-airbase
export BORS_HOST=/var/www/bors/bors-host

export PATH=$PATH:/home/balancer/bin

cd /var/www/bors/bors-airbase/tools/tasks
/var/www/bors/composer/vendor/balancer/bors-ext/cli/tasks/task-processors-start.sh
