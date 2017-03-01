-- create_transactions_table

CREATE TABLE IF NOT EXISTS transactions (

	-- Primary Key
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,

	-- Attributes
	value DECIMAL(10, 2) NOT NULL,
	cash DECIMAL(10, 2) NOT NULL,

	-- Foreign Keys
	portfolio_id INT UNSIGNED NOT NULL,	
	FOREIGN KEY (portfolio_id) REFERENCES portfolios(id),

	-- Timestamps
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	-- Primary Key
	PRIMARY KEY (id)

)
