#!/usr/bin/env bash

# Store the SSH key.
if ! [ -z "$SATIS_SSH_KEY" ]; then
    mkdir -p $HOME/.ssh
    echo "$SATIS_SSH_KEY" > $HOME/.ssh/id_rsa
    # Generate a corresponding public key.
    ssh-keygen -y -f $HOME/.ssh/id_rsa > $HOME/.ssh/id_rsa.pub
    ssh-keyscan github.com >> "$HOME/.ssh/known_hosts"
fi

# Generate the Satis config
php bin/generate-satis-config.php

# Perform an initial build when the instance starts.
./vendor/bin/satis build --no-interaction --skip-errors
