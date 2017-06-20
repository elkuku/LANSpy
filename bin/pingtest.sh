#!/usr/bin/env bash

ROOT=`realpath "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"`

test1=$(ping -c 4 google.com | tail -1| awk '{print $4}' | cut -d '/' -f 2)
test2=$(ping -c 4 github.com | tail -1| awk '{print $4}' | cut -d '/' -f 2)

echo $(date +"%Y%m%d%H%M") ${test1} ${test2} >> ${ROOT}/results/pingtest$(date +"%Y%m%d").txt
