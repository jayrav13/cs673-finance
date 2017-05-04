<?php

	// Establish configurations.
	require("../includes/config.php");

	// Make sure the portfolio id is available as a GET parameter.
	if (empty($_GET["id"]))
	{
		redirect('./');
	}

	// GET the portfolio and tickers at this id for this user.
	$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);
	$tickers = CS50::query("SELECT * FROM tickers WHERE portfolio_id = ?", $_GET["id"]);

	// Generate ER's and Betas.
	$result = get_expected_return_beta($portfolio, $tickers);

	/**
	 *
	 *	Combinations: (<constraint>, null), (null, <constraint>), (<constraint>, <constraint>)
	 *
	 */
	$result["request"] = [
		"expected_return" => $result["portfolio"]["statistics"]["expected_return"] + $result["portfolio"]["statistics"]["expected_return"] * 0.1,
		"beta" => $result["portfolio"]["statistics"]["beta"] - $result["portfolio"]["statistics"]["beta"] * 0.1
	];

	echo json_encode($result);
	exit;

	$optimized = [];
	exec("python ../storage/scripts/portfolio.py '" . json_encode($result) . "'", $optimized);

	$optimized = json_decode($optimized[ count($optimized) - 1 ]);
	header("Content-Type: application/json");
	echo json_encode($optimized);


