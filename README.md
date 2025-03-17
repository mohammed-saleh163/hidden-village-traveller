## Runing the API
### 1. Add your DB credentials to the `.env` file

### 2. Run the following commands in the terminal to install the dependencies, run migrations, and serve the API:
- `$composer install` to install the composer dependencies
- `$npm install` to install the NPM dependencies
- `$php artisan migrate` migrate
- `$php artisan serve`

### 3. To run the automated test suites:
- `$php artisan test`

### 4. No Postman 👎
I hate Postman as much as any sane person would do, so instead you can just run:

    php artisan api:paths {source} {destination}

Make sure to have the server up and running before executing the command.