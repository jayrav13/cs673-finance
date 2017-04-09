<?php

	// configuration
	require("../includes/config.php");

	// var_dump( beta_stock( historical_index( "sp500", "TWTR" ), historical_stock( "TWTR" ) ) );

	$stocks = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
	dump($stocks);