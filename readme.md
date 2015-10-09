# Wichitawesome

An awesome event calendar for Wichita, KS.

## Getting Started

Wichitawesome is built with [Laravel](http://laravel.com).

### Install Dependencies

Run [Composer](http://getcomposer.org/) in the root directory.

    composer install

Ensure that you have [Node.js](http://nodejs.org/download/) on your machine, it is required to install Gulp and Elixir in the next steps.

Frontend assets are compiled with [Laravel Elixir](http://laravel.com/docs/5.1/elixir).

First, install [Gulp](http://gulpjs.com).

    npm install --global gulp

Then, install Elixir.

    npm install

Run `gulp` in the root directory to begin watching for file changes in `/resources/assets`.

### Setup Database

Create a new MYSQL database.

Copy `.env.example`, rename it to `.env`, and update it with your database info. If you are using [MAMP](https://mamp.info), you might have to add `DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock` to the `.env` file to get the database connection working.

Once your database connection is setup, run migrations.

    php artisan migrate

Then, seed the database. 

    php artisan db:seed