# Wichitawesome

An awesome event calendar for Wichita, KS.

## Getting Started

Wichitawesome is built with [Laravel](http://laravel.com).

### Install Dependencies

Run [Composer](http://getcomposer.org/) in the root directory.

    composer install

### Setup Database

Create a new MYSQL database.

Copy `.env.example`, rename it to `.env`, and update it with your database info. If you are using [MAMP](https://mamp.info), you might have to add `DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock` to the `.env` file to get the database connection working.

Once your database connection is setup, run migrations.

    php artisan migrate

Then, seed the database. 

    php artisan db:seed