# Database

Here are the tools you need to build this database locally.

## Migrations

Every file in the `migrations/` folder should start with `##_` so that they can be executed in order, from `01_` to `99_`.

To migrate the database schema, simply do the following:

```bash
cd migrations/
php migrate.php
```

## Reset

It is recommended that you write a few scripts to easily reset this database. Here is an example:

```sql
-- delete.sql

DROP DATABASE IF EXISTS cs673;
CREATE DATABASE cs673;
```

```bash
# reset.sh

mysql -u user -p < delete.sql
cd migrations/
php migrate.php
```