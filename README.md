# CS673 - New York Stock Exchange Portfolio

Welcome to our team's repository!

## Getting Started

### Clone Project

To get started with this project, use the `git` command line tool to clone this project to your machine:

```bash
git clone https://github.com/jayrav13/cs673-finance.git
cd cs673-finance
```

Now you're in the project folder that you just cloned.

### Configuration File

Next, create a `config.json` file using the `example.config.json` file provided:

```bash
cp example.config.json config.json
```

Update your database credentials in `config.json` accordingly. Make sure you create the appropriate database as well.

### Database Migrations

Then, execute the following to migrate the database schema over:

```bash
cd database/migrations/
php migrate.php
```

You'll see confirmations printed to your terminal:

```bash
Query executed: 01_create_users_table.sql
.
.
.
```

### Generate Vendor Files via Composer

First, make sure you have `composer` installed. Details can be found here: https://getcomposer.org/

Composer is a package manager that helps us install external libraries into our project. You'll know it's installed when you do the following:

```bash
$ composer

   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer version 1.3.2 2017-01-27 18:23:41

Usage:
  command [options] [arguments]

Options:
  -h, --help                     Display this help message
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi                     Force ANSI output
      --no-ansi                  Disable ANSI output
  -n, --no-interaction           Do not ask any interactive question
      --profile                  Display timing and memory usage information
      --no-plugins               Whether to disable plugins.
  -d, --working-dir=WORKING-DIR  If specified, use the given directory as working directory.
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  about           Short information about Composer
  archive         Create an archive of this composer package
  ...
```

To INSTALL, execute the following command at the root of the project (i.e. in the same folder as the `composer.json` file):

```bash
$ composer install
```

A folder called `vendor/` will be generated. In this folder, you'll find a file called `autoload.php`. Currently, the `includes/config.php` file imports this when the project runs. So, all of the libraries will be loaded during every HTTP request lifecycle.

### Run Server

Now we're ready to kick this project off! Navigate back to the root of the project (from `database/migrations`, you'll execute `cd ../..`) and do the following:

```bash
cd public
php -S localhost:8000 index.php
```

PHP comes with a built in development server that we'll use. This command runs this project locally on port `8000`. Go to your browser of choice and visit `http://localhost:8000`.
