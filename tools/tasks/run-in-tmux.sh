#!/bin/bash

sudo -u balancer tmux new-session -d -s main
sudo -u balancer tmux new-window -t main -n 'cmd' '/var/www/bors/bors-airbase/tools/tasks/run.sh'
