<?php

	// configuration
	require("../includes/config.php");

	// Check for GET parameter.
	if (empty($_GET["id"]))
	{
		redirect('./');
	}

	$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
	if (count($portfolio) != 1)
	{
		redirect('./');
	}

	$portfolio = $portfolio[0];
	$tickers = CS50::query("SELECT * FROM tickers WHERE portfolio_id = ?", $portfolio["id"]);

	// Static data, can be database'd later.
	$exchanges = [
		"nyse" => "New York Stock Exchange",
		"nasdaq" => "NASDAQ",
		"nse" => "National Stock Exchange of India Ltd."
	];

	$output = [
		"portfolio" => $portfolio,
		"tickers" => $tickers,
		"title" => "Holdings",
		"subtitle" => $portfolio["name"],
		"exchanges" => $exchanges
	];

	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
		render('portfolio.php', $output);
	}
	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{

		if (empty($_POST["ticker"]) || empty($_POST["shares"]))
		{
			$output["errors"] = ['Be sure to provide a stock symbol and number of shares!'];
			render('portfolio.php', $output);
		}

		if(!is_numeric($_POST["shares"]))
		{
			$output["errors"] = ['The number of shares must be numeric!'];
			render('portfolio.php', $output);
		}

		$_POST["ticker"] = strtoupper($_POST["ticker"]);
		$stock = ticker($_POST["ticker"], $_POST["exchange"] ?: null);

		if( ! $stock )
		{
			$output['errors'] = [$_POST["ticker"] . ' in the ' . $exchanges[$_POST["exchange"]] . ' could not be found.'];
			render('portfolio.php', $output);
		}

		if ( !empty($_POST["price"]) )
		{
			$output["price"] = floatval($_POST["price"]);
		}

		$cost = $_POST["shares"] * $stock["price"];
		$user = CS50::query("SELECT * FROM users WHERE id = ?", $_SESSION["cs673_id"]);

		if(count($user) != 1)
		{
			redirect('./');
		}

		$user = $user[0];

		if ($cost > $user["cash"])
		{
			$output["errors"] = ["This will cost $ {$cost}. You only have {$user['cash']} available to spend!"];
			render('portfolio.php', $output);
		}

		$holding = CS50::query("SELECT * FROM tickers WHERE symbol = ?", $stock["ticker"]);

		if (count($holding) > 0)
		{
			$output["errors"] = ["You already own {$stock["ticker"]}! Currently, you cannot purchase more without first selling. Sorry!"];
			render('portfolio.php', $output);
		}

		$update = CS50::query("INSERT INTO tickers (symbol, name, exchange, shares, price, portfolio_id) VALUES (?, ?, ?, ?, ?, ?)", $stock["ticker"], $stock["name"], $exchanges[strtolower($stock["exchange"])], $_POST["shares"], $stock["price"], $_GET["id"]);
		if (count($update) != 1)
		{
			$output["errors"] = ["Something went wrong - please try again."];
			render('portfolio.php', $output);
		}

		$update = CS50::query("UPDATE users SET cash = cash - ? WHERE id = ?", $cost, $_SESSION["cs673_id"]);
		if (count($update) != 1)
		{
			$output["errors"] = ["Something went wrong - please try again."];
			render('portfolio.php', $output);
		}

		redirect("./portfolio.php?id={$_GET["id"]}");


	}