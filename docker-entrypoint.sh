#!/bin/sh
set -e

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Pass control back to the CMD command from Dockerfile
exec "$@"
