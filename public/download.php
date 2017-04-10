<?php

	// configuration
	require("../includes/config.php");

	// Static permalink to name conversions.
	$exchanges = [
		"nyse" => "New York Stock Exchange",
		"nasdaq" => "NASDAQ",
		"nse" => "National Stock Exchange of India Ltd."
	];

	// Set up output headers.
	$headers = [
		"id",
		"symbol",
		"name",
		"exchange",
		"shares",
		"purchase_price",
		"current_price",
		"expected_return_percent",
		"expected_return_value",
		"beta",
		"currency",
		"purchased_on",
		"portfolio",
	];

	// Create overall CSV array.
	$csv = [
		$headers
	];

	// Dump headers w/empty file if the GET variable doesn't exist.
	if( ! array_key_exists("portfolio_id", $_GET) )
	{
		array_to_csv_download($csv);
		exit();
	}

	// Get portfolio. Dump headers w/empty file if the GET variable doesn't exist.
	$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["portfolio_id"], $_SESSION["cs673_id"]);
	if(count($portfolio) == 0)
	{
		array_to_csv_download($csv);
		exit();
	}
	$portfolio = $portfolio[0];

	// Get all tickers.
	$tickers = CS50::query("SELECT * FROM tickers WHERE portfolio_id = ?", $_GET["portfolio_id"]);

	foreach($tickers as $ticker)
	{
		$live_price = live_price($ticker["symbol"], $ticker["exchange"]);
		$expected_return = stock_expected_return($ticker["symbol"], $ticker["exchange"]);

		$historical_stock = historical_stock( $ticker["symbol"], $ticker["exchange"] );
		$historical_index = historical_index( $ticker["currency"] == "INR" ? "niftyfifty" : "sp500" );
		$beta = beta_stock($historical_index, $historical_stock);

		array_push($csv, [
			$ticker["id"],
			$ticker["symbol"],
			$ticker["name"],
			$exchanges[$ticker["exchange"]],
			$ticker["shares"],
			$ticker["price"],
			$live_price,
			$expected_return,
			$expected_return * $ticker["shares"] * $live_price,
			beta_stock($historical_index, $historical_stock),
			$ticker["currency"],
			$ticker["created_at"],
			$portfolio["name"],
		]);
	}

	array_to_csv_download($csv);
	exit();
