<?php

	// Initializing the ini file.
	$ini_array = parse_ini_file("portfolio.ini");

	// configuration
	require("../includes/config.php");

	// Check for GET parameter.
	if (empty($_GET["id"]))
	{
		redirect('./');
	}

	//$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
	// Review this code. I am getting the select query from ini file.
        $portfolio = CS50::query(.$ini_array['SELECT_PORTFOLIO'])
	if (count($portfolio) != 1)
	{
		redirect('./');
	}

	$portfolio = $portfolio[0];
	$tickers = CS50::query("SELECT * FROM tickers WHERE portfolio_id = ?", $portfolio["id"]);

	for($i = 0; $i < count($tickers); $i++)
	{
		$tickers[$i]["current_price"] = live_price($tickers[$i]["symbol"], $tickers[$i]["exchange"]);
		$tickers[$i]["delta"] = $tickers[$i]["current_price"] - $tickers[$i]["price"];
	}

	// Static data, can be database'd later.
	$exchanges = [
		"nyse" => "New York Stock Exchange",
		"nasdaq" => "NASDAQ",
		"nse" => "National Stock Exchange of India Ltd."
	];

	$currency = [
		"nyse" => "USD",
		"nasdaq" => "USD",
		"nse" => "INR"
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

		// Check to make sure the user has provided a ticker and number of shares.
		if (empty($_POST["ticker"]) || empty($_POST["shares"]) || empty($_POST["exchange"]))
		{
			$output["errors"] = ['Be sure to provide a stock symbol, number of shares and exchange!'];
			render('portfolio.php', $output);
		}

		// Check if it is currently owned.
		// We should not tell the user to sell the owned ticker/stock before buying; Example, You own 10 shares of IBM at 100 USD per share
		// Tomorrow, you may want to add another 20 shares of IBM at a different price,say 90 USD per share; 
		//Result will have 30 shares of IBM with a total value of (10*100 + 20*90). We will have to change this logic. just add it and portfolio should be updated correctly.
		$result = CS50::query("SELECT * FROM tickers WHERE symbol = ? AND portfolio_id = ?", $_POST["ticker"], $_GET["id"]);
		if(count($result) > 0)
		{
			$_POST["ticker"] = strtoupper($_POST["ticker"]);
			$output["errors"] = ["You already own shares of {$_POST['ticker']}! Sell them before buying again."];
			render('portfolio.php', $output);
		}

		// Make sure the number of shares is numeric.
		// Number of shares when you buy must be numeric, but the shares holding for a particular stock might differ.
		// Let us assume you own 10 shares of AAPL, 100 USD per share; Apple does well in one financial quarter or in a year 
		//and gives dividend(pay some peanuts back) to the user.Let us assume, we get 20 USD as dividend.
		// In such case, money received will be converted in terms of shares,might not be numeric in that case.In this case, our total number shares for AAPL will be 10.2
	/*	if(!is_numeric($_POST["shares"]))
		{
			$output["errors"] = ['The number of shares must be numeric!'];
			render('portfolio.php', $output);
		} */

		// Bump the ticker to uppercase and get details.
		$_POST["ticker"] = strtoupper($_POST["ticker"]);
		$stock = ticker_info($_POST["ticker"], strtoupper($_POST["exchange"]));

		// Throw error if the ticker can't be found to purchase.
		// We should not throw an error,instead we can just show a message to the user..
		if( ! $stock )
		{
			$output['errors'] = [$_POST["ticker"] . ' in the ' . $exchanges[$_POST["exchange"]] . ' could not be found.'];
			render('portfolio.php', $output);
		}

		// Pricing
		$price = null;
		$historicals = CS50::query("SELECT * FROM actions WHERE symbol = ? AND portfolio_id = ?", $_POST["ticker"], $_GET["id"]);

		// If this has never been purchased before, use the 1/17 price.
		if(count($historicals) == 0)
		{
			$price = init_price($_POST["ticker"], $_POST["exchange"]);
		}
		// If it has been purchased before, use live or inserted.
		else
		{
			if( !empty($_POST["price"]) )
			{
				$price = floatval($_POST["price"]);
			}
			else
			{
				$price = live_price($stock["ticker"], $stock["exchange"]);
			}
		}

		// Generate cost.
		$cost = intval($_POST["shares"]) * $price;

		// Retrieve User object.
		$user = CS50::query("SELECT * FROM users WHERE id = ?", $_SESSION["cs673_id"]);
		if(count($user) != 1)
		{
			redirect('./');
		}
		$user = $user[0];

		// Make sure user can afford this.
		if ($cost > $user["cash"])
		{
			$output["errors"] = ["This will cost $ {$cost}. You only have {$user['cash']} available to spend!"];
			render('portfolio.php', $output);
		}

		$update = CS50::query("
			INSERT INTO tickers (
				symbol,
				name,
				exchange,
				shares,
				price,
				currency,
				portfolio_id
			) VALUES (?, ?, ?, ?, ?, ?, ?)", $stock["ticker"], $stock["name"], strtolower($stock["exchange"]), $_POST["shares"], $price, $currency[$_POST["exchange"]], $_GET["id"]);
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
