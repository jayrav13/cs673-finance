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

### Run Server

Now we're ready to kick this project off! Navigate back to the root of the project (from `database/migrations`, you'll execute `cd ../..`) and do the following:

```bash
cd public
php -S localhost:8000 index.php
```

PHP comes with a built in development server that we'll use. This command runs this project locally on port `8000`. Go to your browser of choice and visit `http://localhost:8000`.
