<?php

	// configuration
	require("../includes/config.php");

	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
		if (empty($_GET["id"]))
		{
			redirect('./');
		}
		else
		{
			$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
			if (count($portfolio) == 1)
			{
				$portfolio = $portfolio[0];
				$tickers = CS50::query("SELECT * FROM tickers WHERE portfolio_id = ?", $portfolio["id"]);

				$output = [
					"portfolio" => $portfolio,
					"tickers" => $tickers,
					"title" => "Holdings",
					"subtitle" => $portfolio["name"]
				];

				render("portfolio.php", $output);

			}
			else
			{
				redirect('./');
			}
		}
	}