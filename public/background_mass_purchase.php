<?php

	// Establish configurations.
	require("../includes/config.php");

	// Make sure the portfolio id is available as a GET parameter.
	if (empty($_GET["id"]))
	{
		redirect('./');
	}

	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
		redirect("./optimize.php?id={$_GET['id']}");
	}

	// GET the portfolio and tickers at this id for this user.
	$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
	$tickers = CS50::query("SELECT * FROM tickers WHERE portfolio_id = ?", $_GET["id"]);

	if($portfolio == null || $tickers == null)
	{
		redirect("./");
	}

	/**
	 *	GET
	 *
	 *	When a user arrives at this page, show two opimized portfolios - ER and Beta.
	 */
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{

		if(empty($_POST["data"]))
		{
			redirect("./optimize.php?id={$_GET['id']}");
		}

		header("Content-Type: application/json");
		$data = json_decode($_POST["data"], true);

		if($data == null)
		{
			redirect("./optimize.php?id={$_GET['id']}");
		}

		$transactions = [];
		$value = $data["extended"]["value"]["current"];

		for($i = 0; $i < count($data["extended"]["tickers"]); $i++)
		{
			$ticker = $tickers[$i];
			$ticker["extended"] = $data["extended"]["tickers"][$i];
			$ticker["new"]["weight"] = $data["optimized"]["x"][$i];
			$ticker["new"]["shares"] = round(($ticker["new"]["weight"] * $value) / ($ticker["extended"]["value"] / $ticker["shares"]));
			$ticker["new"]["delta_shares"] = $ticker["new"]["shares"] - $ticker["shares"];
			$ticker["new"]["sell_all"] = ($ticker["shares"] + $ticker["new"]["delta_shares"] == 0 ? true : false);

			// Continue if there is no change in shares.
			if($ticker["new"]["delta_shares"] == 0)
			{
				continue;
			}

			$ticker["new"]["transaction_type"] = ($ticker["new"]["delta_shares"] < 0 ? "SELL" : "BUY");
			$income = $ticker["extended"]["value"] * ($ticker["new"]["transaction_type"] == "SELL" ? 1 : -1);
			$cash = CS50::query("SELECT * FROM portfolios WHERE id = ?", $_GET["id"]);

			// Pass if I can't afford this transaction.
			if(($income * -1) > $cash[0]["cash"])
			{
				continue;
			}

			// Update cash
			$update = CS50::query("UPDATE portfolios SET cash = cash + ? WHERE id = ? and user_id = ?", $income, $_GET["id"], $_SESSION["cs673_id"]);
			$update = CS50::query("UPDATE tickers SET shares = shares + ? WHERE portfolio_id = ? AND symbol = ?", $ticker["new"]["delta_shares"], $_GET["id"], $ticker["symbol"]);

			// Update price by taking average.
			if($ticker["new"]["transaction_type"] == "BUY")
			{
				$average_price = ($ticker["shares"] * $ticker["price"] + $ticker["new"]["delta_shares"] * $ticker["extended"]["current_price"]) / ($ticker["shares"] + $ticker["new"]["delta_shares"]);
				$update = CS50::query("UPDATE tickers SET price = ? WHERE symbol = ? AND portfolio_id = ?", $average_price, $ticker["symbol"], $_GET["id"]);
			}

			if($ticker["new"]["sell_all"] == true)
			{
				$update = CS50::query("DELETE FROM tickers WHERE symbol = ? AND portfolio_id = ?", $ticker["symbol"], $_GET["id"]);
			}

			$update = CS50::query(file_get_contents('../database/queries/insert_into_actions.sql'), $ticker["symbol"], $ticker["name"], $ticker["exchange"], $ticker["shares"], $ticker["extended"]["current_price"], $ticker["currency"], $ticker["new"]["transaction_type"], $_GET["id"]);

			array_push($transactions, $ticker);
		}

		// echo json_encode($transactions);

		redirect("./portfolio.php?id={$_GET['id']}");

	}


