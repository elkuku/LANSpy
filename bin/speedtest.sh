#!/usr/bin/env bash

ROOT=`realpath "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/.."`

${ROOT}/lib/speedtest/speedtest.py --no-pre-allocate --json >> ${ROOT}/results/speedtest-$(date +"%Y-%m-%d").txt
