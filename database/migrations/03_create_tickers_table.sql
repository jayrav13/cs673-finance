-- create_tickers_table

CREATE TABLE IF NOT EXISTS tickers (

	-- Primary Key
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,

	-- Attributes
	symbol VARCHAR(255) NOT NULL,
	shares INT NOT NULL,
	price FLOAT(10, 2) NOT NULL,

	-- Foreign Keys
	portfolio_id INT UNSIGNED NOT NULL,
	FOREIGN KEY (portfolio_id) REFERENCES portfolios(id),

	-- Timestamps
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	-- Primary Key
	PRIMARY KEY (id)

)