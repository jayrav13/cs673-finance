-- create_portfolios_table

CREATE TABLE IF NOT EXISTS portfolios (

	-- Primary Key
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,

	-- Attributes
	name VARCHAR(255) NOT NULL,

	-- Foreign Keys
	user_id INT UNSIGNED NOT NULL,
	FOREIGN KEY (user_id) REFERENCES users(id),
	
	-- Cash
	balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,

	-- Timestamps
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	-- Primary Key
	PRIMARY KEY (id)

)
