-- create_branches_table.sql

CREATE TABLE IF NOT EXISTS users (

    -- Primary Key
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- Attributes
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(64) NOT NULL,
    cash DECIMAL(10, 2) NOT NULL DEFAULT 0.00,

    -- Timestamps
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Primary Key
    PRIMARY KEY (id)
    
);
