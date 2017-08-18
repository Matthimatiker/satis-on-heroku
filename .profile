#!/usr/bin/env bash

# Generate the Satis config
php bin/render-template.php views/satis.json.twig > satis.json

# Store the SSH key.
if ! [ -z "$SATIS_SSH_KEY" ]; then
    mkdir -p $HOME/.ssh
    echo "$SATIS_SSH_KEY" > $HOME/.ssh/id_rsa
    # Remove the private key from environment variables,
    # it should not be used directly by the application.
    unset $SATIS_SSH_KEY

    # Generate a corresponding public key.
    ssh-keygen -y -f $HOME/.ssh/id_rsa > $HOME/.ssh/id_rsa.pub
    # Make the public key available via environment variable.
    # It is perfectly fine to publish this key. Therfore, the
    # application may access it.
    export SATIS_SSH_PUBLIC_KEY="`cat $HOME/.ssh/id_rsa.pub`"

    # Add hosts of all configured repositories to known_hosts
    php bin/print-repository-hosts.php | xargs ssh-keyscan -t rsa >> "$HOME/.ssh/known_hosts"
fi

# Activate authentication if requested.
if ! [ -z "$SATIS_AUTH_USERNAME" ]; then
    htpasswd -c -b -B "$HOME/.htpasswd" "$SATIS_AUTH_USERNAME" "$SATIS_AUTH_PASSWORD"
    php bin/render-template.php views/htaccess.text.twig > web/.htaccess
fi

# Perform an initial build when the instance starts.
./vendor/bin/satis build --no-interaction --skip-errors

if [ "$SATIS_GITHUB_MANAGE_WEBHOOKS" == "1" ]; then
    # Create and update webhooks in the background.
    ./bin/activate-webhooks.php &
fi
