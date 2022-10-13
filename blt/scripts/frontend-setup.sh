#!/bin/bash

# Fail if there is an error.
set -e

# Setup nvm.
. ~/.nvm/nvm.sh
nvm install
nvm use

echo 'Installing theme build files...'
cd $1/docroot/"${THEME_DEFAULT_PATH}"/gnorm
npm ci
