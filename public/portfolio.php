<?php

	// configuration
	require("../includes/config.php");

	// Check for GET parameter.
	if (empty($_GET["id"]))
	{
		redirect('./');
	}

	$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
	
	// Review this code. I am getting the select query from ini file.
        //$portfolio = CS50::query(.$ini_array['SELECT_PORTFOLIO'])
	
	if (count($portfolio) != 1)
	{
		redirect('./');
	}

	$portfolio = $portfolio[0];
	$tickers = CS50::query("SELECT * FROM tickers WHERE portfolio_id = ?", $portfolio["id"]);

	$value = [
		"current" => 0,
		"original" => 0
	];

	for($i = 0; $i < count($tickers); $i++)
	{
		$tickers[$i]["current_price"] = live_price($tickers[$i]["symbol"], $tickers[$i]["exchange"]);
		$tickers[$i]["delta"] = $tickers[$i]["current_price"] - $tickers[$i]["price"];
		if( $tickers[$i]["currency"] == "USD" )
		{
			$value["current"] += $tickers[$i]["current_price"] * $tickers[$i]["shares"];
			$value["original"] += $tickers[$i]["price"] * $tickers[$i]["shares"];
		}
		else
		{
			$value["current"] += currency_converter( "INR", "USD", $tickers[$i]["current_price"], true ) * $tickers[$i]["shares"];
			$value["original"] += currency_converter( "INR", "USD", $tickers[$i]["price"], true ) * $tickers[$i]["shares"];
		}
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
		"exchanges" => $exchanges,
		"value" => $value
	];

	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
		render('portfolio.php', $output);
	}
	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{

		if ( ! array_key_exists("action", $_GET) )
		{
			$output['errors'] = ['Something went wrong - please try again!'];
			render('portfolio.php', $output);
		}

		if ($_GET['action'] == 'cash')
		{

			if( !empty($_POST["operation"]) && !empty($_POST["cash"]) )
			{
				$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
				if(count($portfolio) != 1)
				{
					redirect('./');
				}
				$portfolio = $portfolio[0];
				$cash = floatval($_POST["cash"]);
				if($cash < 0)
				{
					$cash = -1 * $cash;
				}

				$operation = intval($_POST["operation"]);
				$transaction = $operation * $cash;

				if($portfolio["cash"] + $transaction < 0)
				{
					$output['errors'] = ["You don't have enough cash for this operation."];
					render("portfolio.php", $output);
				}
				else
				{
					$update = CS50::query("UPDATE portfolios SET cash = ? WHERE id = ? AND user_id = ?", $transaction + $portfolio["cash"], $_GET["id"], $_SESSION["cs673_id"]);
					$update = CS50::query("INSERT INTO transactions (value, cash, portfolio_id) VALUES (?, ?, ?)", $transaction, $transaction + $portfolio["cash"], $_GET["id"]);
				}

				redirect('/portfolio.php?id=' . $_GET['id']);

			}

		}

		else if ($_GET['action'] == 'shares')
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
			$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
			if(count($portfolio) != 1)
			{
				"AHHH";
				exit();
			}
			$portfolio = $portfolio[0];

			// Make sure user can afford this.
			if ($cost > $portfolio["cash"])
			{
				$output["errors"] = ["This will cost $ {$cost}. You only have {$portfolio['cash']} available to spend in this portfolio!"];
				render('portfolio.php', $output);
			}

			$update = CS50::query(file_get_contents('../database/queries/insert_into_tickers.sql'), $stock["ticker"], $stock["name"], strtolower($stock["exchange"]), $_POST["shares"], $price, $currency[$_POST["exchange"]], $_GET["id"]);
			if (count($update) != 1)
			{
				$output["errors"] = ["Something went wrong - please try again."];
				render('portfolio.php', $output);
			}

			$update = CS50::query(file_get_contents('../database/queries/insert_into_actions.sql'), $stock["ticker"], $stock["name"], strtolower($stock["exchange"]), $_POST["shares"], $price, $currency[$_POST["exchange"]], "BUY", $_GET["id"]);

			$update = CS50::query("UPDATE portfolios SET cash = cash - ? WHERE id = ? AND user_id = ?", $cost, $_GET["id"], $_SESSION["cs673_id"]);
			if (count($update) != 1)
			{
				$output["errors"] = ["Something went wrong - please try again."];
				render('portfolio.php', $output);
			}

			redirect("./portfolio.php?id={$_GET["id"]}");

		}

	}
