-- create_actions_table

CREATE TABLE IF NOT EXISTS actions (

	-- Primary Key
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,

	-- Attributes
	symbol VARCHAR(255) NOT NULL,
	name VARCHAR(255) NOT NULL,
	exchange VARCHAR(255) NOT NULL,
	shares INT NOT NULL,
	price DECIMAL(10, 2) NOT NULL,
	currency VARCHAR(255) NOT NULL,
	action VARCHAR(255) NOT NULL, -- BUY, SELL

	-- Foreign Keys
	portfolio_id INT UNSIGNED NOT NULL,
	FOREIGN KEY (portfolio_id) REFERENCES portfolios(id),

	-- Timestamps
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	-- Primary Key
	PRIMARY KEY (id)

)