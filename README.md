# Crypto Rate API

This is a web application that provides API endpoints to fetch and store cryptocurrency exchange rates.

## Features

- Fetch live exchange rates for Bitcoin (BTC) in multiple currencies (USD, EUR, GBP).
- Store and retrieve historical exchange rates.
- API endpoints for querying and updating rates.
- Scalable with any database backend (e.g., MySQL, PostgreSQL, or SQLite).

## Requirements

- PHP 8.x
- Composer (install via [Composer website](https://getcomposer.org/))
- Symfony CLI (optional, but recommended for development)
- A database (MySQL, PostgreSQL, or SQLite)

### Setup

## Clone the repository:
   git clone https://github.com/your-repo/crypto-rate-api.git
   cd crypto-rate-api



## install dependencies::
    composer install


## Configure .env: Edit your .env file to include the database connection details:
    DATABASE_URL="mysql://user:user111@db:3306/crypto_api"

## Set up the database::
    php bin/console doctrine:migrations:migrate


### Usage
    Endpoints
  * Fetch Historical Rates
    GET /historical-rates

  * Query Parameters:

    **pair** - Currency pair (e.g., BTC/USD).
    **start_date** - Start of date range (e.g., 2024-01-01T00:00:00).
    **end_date** - End of date range (e.g., 2024-01-31T23:59:59).

  * Example:

    ```bash
    curl "http://localhost:8000/historical-rates?pair=BTC/USD&start_date=2024-01-01T00:00:00&end_date=2024-01-31T23:59:59"
    ```
    
  * Trigger Rate Updates
    **POST /update-rates**

  * Triggers the application to fetch the latest rates from the external API.

    Example:
    ```bash
    curl -X POST "http://localhost:8000/update-rates"
    ```
  * Update courses via CRON or manually with commands
    ```bash
    php bin/console app:update-rates
    ```

## Tests
    Run tests using:
```bash
php bin/phpunit
```

## Deployment
    Build and run the Docker container:
```bash
docker-compose up --build
```
Access the app at http://localhost:8000

## Cron Setup 
    (If you want the task to run, for example, at 10 minutes of every hour, you will need to replace 15 with 0)
```bash
15 * * * * /usr/bin/php /path/to/your/project/bin/console app:update-rates
```