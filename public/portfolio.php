<?php

	// Establish configurations.
	require("../includes/config.php");
	setlocale(LC_MONETARY, 'en_US');

	// Make sure the portfolio id is available as a GET parameter.
	if (empty($_GET["id"]))
	{
		redirect('./');
	}

	// GET the portfolio at this id for this user.
	$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);

	// In the event that the portfolio does not exist or issues arise, redirect. Otherwise, get the first row (i.e. the portfolio) and continue.
	if (count($portfolio) != 1)
	{
		redirect('./');
	}
	$portfolio = $portfolio[0];
	$portfolio["total_projection"] = 0;
	$portfolio["statistics"] = [
		"beta" => 0,
		"expected_return" => 0
	];

	// Get all of the tickers in the portfolio.
	$tickers = CS50::query("SELECT * FROM tickers WHERE portfolio_id = ?", $portfolio["id"]);

	// Prepare a value dict to store how much it's worth.
	$value = [
		"current" => 0,
		"original" => 0
	];

	$market_distro = [
		"USD" => 0,
		"INR" => 0,
	];

	// Conduct calculations per ticker.
	for($i = 0; $i < count($tickers); $i++)
	{

		// Get the live stock price of this ticker.
		$tickers[$i]["current_price"] = live_price($tickers[$i]["symbol"], $tickers[$i]["exchange"]);

		// Calculate the price change between the live price and the price at which this was purchased (in native currency).
		$tickers[$i]["delta"] = $tickers[$i]["current_price"] - $tickers[$i]["price"];

		// Calculate both the current and original portfolio values, this time converting foreign currencies to USD.
		if( $tickers[$i]["currency"] == "USD" )
		{
			$tickers[$i]["value"] = $tickers[$i]["current_price"] * $tickers[$i]["shares"];
			$value["current"] += $tickers[$i]["value"];
			$value["original"] += $tickers[$i]["price"] * $tickers[$i]["shares"];
		}
		else
		{
			$tickers[$i]["value"] = currency_converter( "INR", "USD", $tickers[$i]["current_price"], true ) * $tickers[$i]["shares"];
			$value["current"] += $tickers[$i]["value"];
			$value["original"] += currency_converter( "INR", "USD", $tickers[$i]["price"], true ) * $tickers[$i]["shares"];
		}

		$market_distro[$tickers[$i]["currency"]] += $tickers[$i]["value"];

		/**
		 *	Expected Return and Beta
		 *
		 *
		 */
		$historical_stock = historical_stock( $tickers[$i]["symbol"], $tickers[$i]["exchange"] );
		$historical_index = historical_index( $tickers[$i]["currency"] == "INR" ? "niftyfifty" : "sp500" );
		$tickers[$i]["historicals"] = [
			"stock" => $historical_stock,
			"index" => $historical_index,
			"beta" => beta_stock($historical_index, $historical_stock),
			"expected_return" => (stock_expected_return($tickers[$i]["symbol"], $tickers[$i]["exchange"])) * $tickers[$i]["value"],
		];

	}

	for($i = 0; $i < count($tickers); $i++)
	{
		$tickers[$i]["weight"] = $tickers[$i]["value"] / $value["current"];
		$portfolio["statistics"]["beta"] += $tickers[$i]["weight"] * $tickers[$i]["historicals"]["beta"];
		$portfolio["statistics"]["expected_return"] += $tickers[$i]["historicals"]["expected_return"] * $tickers[$i]["weight"];
	}

	// Collect all transactions (cash) and actions (stocks) - historicals.
	$transactions = CS50::query("SELECT * FROM transactions WHERE portfolio_id = ? ORDER BY created_at DESC", $_GET["id"]);
	$actions = CS50::query("SELECT * FROM actions WHERE portfolio_id = ? ORDER BY created_at DESC", $_GET["id"]);

	// Static permalink to name conversions.
	$exchanges = [
		"nyse" => "New York Stock Exchange",
		"nasdaq" => "NASDAQ",
		"nse" => "National Stock Exchange of India Ltd."
	];

	// Static permalink to currency conversions.
	$currency = [
		"nyse" => "USD",
		"nasdaq" => "USD",
		"nse" => "INR"
	];

	$bought_messages = [
		"init" => "Successfully purchased at 1/17/2017 price.",
		"custom" => "Successfully purchased at custom price.",
		"live" => "Successfully purchased at live price (per Google Finance)"
	];

	// Generate a dictionary of data that ALL rendered versions of the portfolio require.
	$output = [
		"portfolio" => $portfolio,
		"tickers" => $tickers,
		"title" => "Holdings",
		"subtitle" => $portfolio["name"],
		"exchanges" => $exchanges,
		"value" => $value,
		"transactions" => $transactions,
		"actions" => $actions,
		"bought_messages" => $bought_messages,
		"market_distro" => $market_distro
	];

	// On a GET request, return the portfolio.php view with standard data.
	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
		if(array_key_exists("bought", $_GET))
		{
			$output["success"] = [$bought_messages[$_GET["bought"]]];
		}
		render('portfolio.php', $output);
	}
	// On a POST request, determine logic to be executed.
	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{

		// All POST requests require a GET "action" key/value. It can either be "cash" or "shares".
		// This is indicated in the HTML. If this errors out, HTML was tampered with.
		if ( ! array_key_exists("action", $_GET) )
		{
			$output['errors'] = ['Something went wrong - please try again!'];
			render('portfolio.php', $output);
		}

		// If the action is "cash", this means user wants to adjust the amount of cash in their portfolio.
		if ($_GET['action'] == 'cash')
		{

			// Make sure required POST data is available.
			if( !empty($_POST["operation"]) && !empty($_POST["cash"]) )
			{

				// Convert cash to floatval.
				$cash = floatval($_POST["cash"]);

				// Convert the cash to positive values only.
				if($cash < 0)
				{
					$cash = -1 * $cash;
				}

				// Determine the operation value (-1, 1) and set transaction to this (negative or positive).
				$operation = intval($_POST["operation"]);
				$transaction = $operation * $cash;

				// Throw error if not enough money.
				if($portfolio["cash"] + $transaction < 0)
				{
					$output['errors'] = ["You don't have enough cash for this operation."];
					render("portfolio.php", $output);
				}
				// Update portfolio's cash, create a new historical transaction.
				else
				{
					$update = CS50::query("UPDATE portfolios SET cash = ? WHERE id = ? AND user_id = ?", $transaction + $portfolio["cash"], $_GET["id"], $_SESSION["cs673_id"]);
					$update = CS50::query("INSERT INTO transactions (value, cash, portfolio_id) VALUES (?, ?, ?)", $transaction, $transaction + $portfolio["cash"], $_GET["id"]);
				}

			}

			redirect('/portfolio.php?id=' . $_GET['id']);

		}

		// If the action is on shares, this means the user is trying to buy or sell shares.
		else if ($_GET['action'] == 'shares')
		{

			if( array_key_exists("method", $_GET) )
			{

				if($_GET["method"] == "buy")
				{

					// Add an upper bound to the number of stocks that may be purchased.
					if(count($tickers) == 10)
					{
						if(count(CS50::query("SELECT * FROM tickers WHERE symbol = ? AND portfolio_id = ?", $_POST["symbol"], $_GET["id"])) == 0)
						{
							$output["errors"] = ["The maximum number of stocks you may purchase for this portfolio is 10."];
							render('portfolio.php', $output);
						}
					}

					// Make sure required POST data is available.
					if (empty($_POST["ticker"]) || empty($_POST["shares"]) || empty($_POST["exchange"]))
					{
						$output["errors"] = ['Be sure to provide a stock symbol, number of shares and exchange!'];
						render('portfolio.php', $output);
					}

					// Check if your current cash value is at least 10% of your portfolio.
					if ($portfolio["cash"] < 0.1 * $value["current"])
					{
						$percentage = 100 * ($portfolio["cash"] / ($portfolio["cash"] + $value["current"]));
						$output["errors"] = ["Your portfolio's cash must account for more than 10% of your portfolio's value. Currently, it accounts for {$percentage}%. Please add more cash to continue."];
						render("portfolio.php", $output);
					}

					// Bump the ticker to uppercase and get details.
					$_POST["ticker"] = strtoupper($_POST["ticker"]);
					$stock = ticker_info($_POST["ticker"], strtoupper($_POST["exchange"]));

					// Throw error if the ticker can't be found to purchase.
					if( ! $stock )
					{
						$output['errors'] = [$_POST["ticker"] . ' in the ' . $exchanges[$_POST["exchange"]] . ' could not be found.'];
						render('portfolio.php', $output);
					}

					// Query for this ticker in this portfolio. If exists, throw error - currently, we only let users buy stocks they don't own at the moment.
					/*
					$result = CS50::query("SELECT * FROM tickers WHERE symbol = ? AND portfolio_id = ?", $stock["ticker"], $_GET["id"]);
					if(count($result) > 0)
					{
						$output["errors"] = ["You already own shares of {$stock['ticker']}! Sell them before buying again."];
						render('portfolio.php', $output);
					}
					*/

					// Make sure the number of shares is numeric.
					if(!is_numeric($_POST["shares"]))
					{
						$output["errors"] = ['The number of shares must be numeric!'];
						render('portfolio.php', $output);
					}

					// Pricing
					$price = null;
					$historicals = CS50::query("SELECT * FROM actions WHERE symbol = ? AND portfolio_id = ?", $stock["ticker"], $_GET["id"]);

					// Establish a variable for showing the price at which the stock was bought.
					$bought_price = "";

					// If this has never been purchased before, use the 1/17 price.
					if(count($historicals) == 0)
					{
						$price = init_price($stock["ticker"], $_POST["exchange"]);
						if($price == false)
						{
							$output["errors"] = ["Cound not retrieve stock price from 1/17/2017 for {$stock['ticker']}!"];
							render("portfolio.php", $output);
						}
						$bought_price = "bought=init";
					}
					// If it has been purchased before, use live or inserted.
					else
					{
						if( !empty($_POST["price"]) )
						{
							$price = floatval($_POST["price"]);
							$bought_price = "bought=custom";
						}
						else
						{
							$price = live_price($stock["ticker"], $stock["exchange"]);
							$bought_price = "bought=live";
						}
					}

					// Generate cost.
					$cost = intval($_POST["shares"]) * $price;

					// Check if a conversion is needed. Will need real time price if historicals exist.
					if($currency[$_POST["exchange"]] != "USD")
					{
						$cost = currency_converter("IND", "USD", $cost, ( count($historicals) == 0 ));
					}
					// Make sure user can afford this.
					if ($cost > $portfolio["cash"])
					{
						$output["errors"] = ["This will cost $ {$cost}. You only have {$portfolio['cash']} available to spend in this portfolio!"];
						render('portfolio.php', $output);
					}

					$result = CS50::query("SELECT * FROM tickers WHERE symbol = ? AND portfolio_id = ?", $stock["ticker"], $_GET["id"]);
					if(count($result) > 0)
					{
						$result = $result[0];
						$new_price = ($result["price"] * $result["shares"] + $price * $_POST["shares"]) / ($result["shares"] + $_POST["shares"]);
						$update = CS50::query("UPDATE tickers SET price = ?, shares = ? WHERE portfolio_id = ? AND id = ?", $new_price, ($result["shares"] + $_POST["shares"]), $_GET["id"], $result["id"]);
					}
					else
					{
						$update = CS50::query(file_get_contents('../database/queries/insert_into_tickers.sql'), $stock["ticker"], $stock["name"], strtolower($stock["exchange"]), $_POST["shares"], $price, $currency[$_POST["exchange"]], $_GET["id"]);
					}

					// Add this to historical table.
					$update = CS50::query(file_get_contents('../database/queries/insert_into_actions.sql'), $stock["ticker"], $stock["name"], strtolower($stock["exchange"]), $_POST["shares"], $price, $currency[$_POST["exchange"]], "BUY", $_GET["id"]);

					// Update cash to reflect this buy.
					$update = CS50::query("UPDATE portfolios SET cash = cash - ? WHERE id = ? AND user_id = ?", $cost, $_GET["id"], $_SESSION["cs673_id"]);

					redirect("./portfolio.php?id={$_GET["id"]}&{$bought_price}");
				}
				else if($_GET["method"] == "sell")
				{
					// Make sure required POST data is available.
					if (empty($_POST["ticker_id"]) || empty($_POST["shares"]))
					{
						$output["errors"] = ['Be sure to provide a stock symbol and number of shares!'];
						render('portfolio.php', $output);
					}

					// Add an lower bound to the number of stocks that may be purchased.
					if(count($tickers) == 7)
					{
						$output["errors"] = ["The minimum number of stocks you may purchase for this portfolio is 7."];
						render('portfolio.php', $output);
					}

					// Check if the number of shares requested is numeric.
					if (!is_numeric($_POST["shares"]))
					{
						$output["errors"] = ['The number of shares must be numeric!'];
						render('portfolio.php', $output);
					}

					// Retrieve ticker per POST variable.
					$ticker = CS50::query("SELECT * FROM tickers WHERE id = ?", $_POST["ticker_id"]);
					if(count($ticker) != 1)
					{
						redirect('./');
					}
					$ticker = $ticker[0];

					// Convert shares to int, check if this is a valid quantity.
					$shares = intval($_POST["shares"]);
					if($ticker["shares"] < $shares)
					{
						$output["errors"] = ["You can only sell up to {$ticker["shares"]} shares!"];
						render('portfolio.php', $output);
					}

					// Get the live price of this ticker.
					$price = live_price($ticker["symbol"], $ticker["exchange"]);
					if($ticker["currency"] != "USD")
					{
						$price = currency_converter("INR", "USD", $price, true);
					}

					$update = CS50::query("UPDATE portfolios SET cash = ? WHERE id = ?", ($portfolio["cash"] + $price * $shares), $_GET["id"]);
					if($ticker["shares"] - $shares == 0)
					{
						$update = CS50::query("DELETE FROM tickers WHERE id = ?", $_POST["ticker_id"]);
					}
					else
					{
						$update = CS50::query("UPDATE tickers SET shares = ? WHERE id = ?", ($ticker["shares"] - $shares), $_POST["ticker_id"]);
					}
					$update = CS50::query(file_get_contents('../database/queries/insert_into_actions.sql'), $ticker["symbol"], $ticker["name"], $ticker["exchange"], $shares, currency_converter("USD", "INR", $price, true), $ticker["currency"], "SELL", $_GET["id"]);

					redirect("./portfolio.php?id={$_GET["id"]}");

				}
				else
				{
					// Method invalid, problem
					redirect('./');
				}

			}
			else
			{
				// Method DNE, problem
				redirect('./');
			}

		}
		// Delete this portfolio.
		else if ($_GET['action'] == 'delete')
		{
			$update = CS50::query("DELETE FROM actions WHERE portfolio_id = ?", $_GET["id"]);
			$update = CS50::query("DELETE FROM transactions WHERE portfolio_id = ?", $_GET["id"]);
			$update = CS50::query("DELETE FROM tickers WHERE portfolio_id = ?", $_GET["id"]);
			$update = CS50::query("DELETE FROM portfolios WHERE id = ?", $_GET["id"]);
			redirect('./');
		}

	}
