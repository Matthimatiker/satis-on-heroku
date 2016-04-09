#!/usr/bin/env bash

# Generate the Satis config
php bin/generate-satis-config.php

# Perform an initial build when the instance starts.
./vendor/bin/satis build --no-interaction
