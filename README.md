# Satis on Heroku #

Your private Satis instance on Heroku, just one click away.

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy)

Use the button above to deploy your own Satis instance. It's free, all you need is a 
[Heroku](https://heroku.com) account.

## Initial Configuration ##

### Connect local checkout to Heroku app ###

It is assumed that you have already installed the [Heroku Toolbelt](https://toolbelt.heroku.com/) and that 
you have logged in via ``heroku login`` command. The toolbelt is necessary to interact with your Heroku app via CLI.

Replace ``[heroku-app-name]`` with the app name you have chosen during deployment on Heroku when following the
instructions in this section.

Checkout the repository:

    git clone git@github.com:Matthimatiker/satis-on-heroku.git [heroku-app-name]
    cd [heroku-app-name]
    
Connect the checkout to your Heroku app:
    
    heroku git:remote --app [heroku-app-name]

You are now able to configure your app from your checkout directory via toolbelt.

### Register a SSH key ###

Your Satis instance needs a SSH key to read package data from private repositories. You can skip this
step if you do not plan to use private repositories.

Generate a new key:

    ssh-keygen -t rsa -f satis_on_heroku_ssh_key
    
Add the generated private key to your app configuration:
    
    heroku config:set SATIS_SSH_KEY="`cat satis_on_heroku_ssh_key`"
    
Ensure that the key can be used to access your private repositories. When using GitHub you have to register the
public key at [https://github.com/settings/keys](https://github.com/settings/keys). You might want to create
a separate account with read-only access for your Satis app.

### Add a GitHub token ###

You can add a GitHub token to ensure that the Satis instance can access private GitHub repositories.

Create a new token at [https://github.com/settings/tokens/new](https://github.com/settings/tokens/new?scopes=repo&description=Satis+on+Heroku).
Afterwards add it as configuration variable to your Satis app:

    heroku config:set SATIS_GITHUB_TOKEN="[your-generated-token]"

### Secure the Satis instance ###

To restrict access you can activate basic authentication for your Satis instance. Just assign a username and password:

    heroku config:set SATIS_AUTH_USERNAME=test SATIS_AUTH_PASSWORD=secret
    
Set the username to an empty value to disable the authentication:
    
    heroku config:set SATIS_AUTH_USERNAME=

### Remove the example repository ###

For demonstration purposes, an example repository has been registered during deployment. You might want 
to remove that now:

    heroku config:unset SATIS_REPOSITORY_EXAMPLE

## Usage ##

### Add repositories ###

Repositories that are scanned for packages are added via ``SATIS_REPOSITORY_*`` config variables. The values
of all config variables that are prefixed with ``SATIS_REPOSITORY_`` are treated as repository urls.

Here is an example how to add a repository:

    heroku config:set SATIS_REPOSITORY_NEW=git@github.com:Matthimatiker/satis-on-heroku.git

You can also use the app settings page on the [Heroku dashboard](https://dashboard.heroku.com) to add config values.

### Package updates ###

A full packages scan is performed during instance startup. Delta updates for GitHub repositories
can be achieved via webhooks.

#### Manage GitHub webhooks automatically ####

Provide a valid GitHub token and set ``SATIS_GITHUB_MANAGE_WEBHOOKS`` to ``1`` to ensure that the
Satis instance manages package update notifications for GitHub repositories automatically via webhooks.
A webhook is registered (and kept up to date) for each GitHub repository that has been added to
your Satis instance.

Now the package repository is updated whenever new code is pushed.

#### Configure GitHub webhook manually ####

If you do not want webhooks to be registered automatically, then you can add the webhooks manually.
Go to your *repository page* -> *Settings* -> *Webhooks and services* and register the following Payload URL
for push events:

    https://[your-app-name].herokuapp.com/github-webhook.php

Note: If you have activated authentication, then you have to encode the credentials in the Payload URL:

    https://user:password:[your-app-name].herokuapp.com/github-webhook.php

#### Update all packages ####

To rebuild the whole package repository you can restart your app:

    heroku restart
    
You can also restart the app via [Heroku dashboard](https://dashboard.heroku.com).
