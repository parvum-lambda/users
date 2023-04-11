#!/bin/bash

SAIL_PATH=./vendor/bin/sail

docker run --rm --interactive --tty --volume "$(pwd)":/app composer install --no-cache --ignore-platform-reqs
docker run --rm --interactive --tty --volume "$(pwd)":/app composer run post-autoload-dump
docker run --rm --interactive --tty --volume "$(pwd)":/app composer run post-root-package-install
docker run --rm --interactive --tty --volume "$(pwd)":/app composer run post-create-project-cmd

$SAIL_PATH up -d
$SAIL_PATH artisan migrate --seed
$SAIL_PATH down