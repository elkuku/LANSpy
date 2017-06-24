#!/usr/bin/env bash

ROOT=`realpath "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/.."`

${ROOT}/bin/console doctrine:database:create
${ROOT}/bin/console doctrine:schema:update --force
${ROOT}/bin/console doctrine:fixtures:load
