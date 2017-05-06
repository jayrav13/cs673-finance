<?php

	// Establish configurations.
	require("../includes/config.php");

	$exchanges = [
		"nyse" => "New York Stock Exchange",
		"nasdaq" => "NASDAQ",
		"nse" => "National Stock Exchange of India Ltd."
	];

	$output = [
		"exchanges" => $exchanges,
		"title" => "Search"
	];

	if($_SERVER["REQUEST_METHOD"] == "GET")
	{
		render('search.php', $output);
	}
	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if(empty($_POST["ticker"]) || empty($_POST["exchange"]))
		{
			$output["errors"] = ["Ticker and Exchange required!"];
			render('search.php', $output);
		}
		else
		{
			$ticker = $_POST["ticker"];
			$exchange = $_POST["exchange"];

			$info = ticker_info($ticker, $exchange);
			$live_price = live_price($ticker, $exchange);
			$init_price = init_price($ticker, $exchange);

			if($info == false || $live_price == false || $init_price == false)
			{
				$output["errors"] = ["Something went wrong - be sure to confirm that this ticker lives in this exchange and please try again!"];
				render('search.php', $output);
			}
			else
			{
				$output["info"] = $info;
				$output["live_price"] = $live_price;
				$output["init_price"] = $init_price;

				render('search.php', $output);
			}
		}

	}
