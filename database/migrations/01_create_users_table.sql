-- create_users_table

CREATE TABLE IF NOT EXISTS users (

    -- Primary Key
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- Attributes
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(64) NOT NULL,
   
    -- Timestamps
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Primary Key
    PRIMARY KEY (id)

);
