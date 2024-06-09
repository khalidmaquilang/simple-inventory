#!/bin/bash

# Laravel Database Migration and Shield Generation

echo "Running database migrations..."
./vendor/bin/sail artisan migrate:refresh

echo "Generating Shield permissions..."
./vendor/bin/sail artisan shield:generate --all --option=permissions

echo "Database migrations and Shield permissions generation complete!"
