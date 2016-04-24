#!/usr/bin/env bash

# Store the SSH key.
if ! [ -z "$SATIS_SSH_KEY" ]; then
    mkdir -p $HOME/.ssh
    echo "$SATIS_SSH_KEY" > $HOME/.ssh/id_rsa
    # Generate a corresponding public key.
    ssh-keygen -y -f $HOME/.ssh/id_rsa > $HOME/.ssh/id_rsa.pub
    touch "$HOME/.ssh/known_hosts"
    chmod -R 600 $HOME/.ssh
fi

# Generate the Satis config
php bin/generate-satis-config.php

# Add hosts of all configured repositories to known_hosts
php bin/print-repository-hosts.php | xargs ssh-keyscan >> "$HOME/.ssh/known_hosts"

# Perform an initial build when the instance starts.
./vendor/bin/satis build --no-interaction --skip-errors
