#!/bin/bash

# export BORS_SITE=/var/www/bors/bors-airbase
# export BORS_HOST=/var/www/bors/bors-host

cd $(dirname $0)
/var/www/bors/bors-ext/cli/tasks/task-processors-start.sh
