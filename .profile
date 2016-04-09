#!/usr/bin/env bash

# Configure Composer to use a GitHub Token if one was provided.
if ! [ -z "$SATIS_GITHUB_TOKEN" ]; then
    vendor/bin/composer config github-oauth.github.com $SATIS_GITHUB_TOKEN
fi

# Generate the Satis config
php bin/generate-satis-config.php

# Perform an initial build when the instance starts.
./vendor/bin/satis build --no-interaction
