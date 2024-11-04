# Laravel Application Setup and Running Guide

## Prerequisites

Before you start, ensure you have the following installed:
- PHP (version 8.1 or higher)
- Composer (dependency manager for PHP)
- A database server (e.g., MySQL)
## 1. Clone the Repository

First, clone the repository of your Laravel application:

```bash
git clone https://github.com/Akumar8080/Lead-app.git
cd Lead-app
```
## 2. Install Dependencies

 - Run Composer to install the PHP dependencies required for the application:

```bash
composer install

```

## 3. Configure the Environment

 - Laravel uses an environment file to manage configuration settings. Copy the example environment file to create your own .env file:

 ```bash
cp .env.example .env

```

## 5. Run Migrations

```bash
php artisan migrate

##Factory store dummy data than use command

php artisan db:seed

```

## 6. Start the Development Server

```bash
php artisan serve

```

## 7. Access Your Application

 - Open your web browser and navigate to http://localhost:8000 (or the port you specified) to see your Laravel application in action.