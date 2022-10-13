#!/bin/bash

# Fail if there is an error.
set -e

# Setup nvm.
. ~/.nvm/nvm.sh
nvm use

echo 'Building main theme...'
cd $1/docroot/"${THEME_DEFAULT_PATH}"/gnorm
npx gulp build
