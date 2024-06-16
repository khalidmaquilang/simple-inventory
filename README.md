## Requirements

- php: 8.3
- Node: 20

## Setup on local

- Copy .env file

````
cp .env.example .env
````

- Edit mysql connection in your .env (credential is based on your sql server)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=password
```

- Run ``composer install``
- Run ``npm install``
- Run ``php artisan key:generate``
- Run ``php artisan migrate --seed``
- Run ``php artisan shield:generate --all --option=permissions``
- Run ``php artisan serve``
- Run ``npm run dev`` (another window)
- Open ``localhost`` on your browser

## Setup on local using sail

- Install Docker https://laravel.com/docs/11.x/installation#docker-installation-using-sail
- Copy .env file

````
cp .env.example .env
````

- Edit mysql connection in your .env (credential is based on your sql server)

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

- Run this command

````
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
````

- Run ``./vendor/bin/sail up -d`` to build and start the container
- Run ``./vendor/bin/sail npm install``
- Run ``./vendor/bin/sail artisan key:generate``
- Run ``./vendor/bin/sail artisan migrate --seed``
- Run ``./vendor/bin/sail artisan shield:generate --all --option=permissions``
- Run ``./vendor/bin/sail npm run dev``
- Open ``localhost`` on your browser

## Deploying Prod

- `composer install --prefer-dist --no-dev -o`
- `php artisan migrate`
- `php artisan icon:cache`
- `php artisan filament:cache-components`
- `php artisan optimize`
