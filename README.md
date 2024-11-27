## Setup Steps

- git clone the repo
- run composer install  (ensure using php8.2)
- cp .env.example .env
- php artisan key:generate
- set your db credentials in the .env file
- set the OPENAI_API_KEY in the .env
- php artisan migrate --seed
