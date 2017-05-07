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

	if($portfolio == null || $tickers == null)
	{
		redirect("./");
	}

	$python = CS50::config()["environment"]["python"];
	// Generate ER's and Betas.
	$result = get_expected_return_beta($portfolio, $tickers);

	/**
	 *	GET
	 *
	 *	When a user arrives at this page, show two opimized portfolios - ER and Beta.
	 */
	if($_SERVER["REQUEST_METHOD"] == "GET")
	{

		// Store optimized results.
		$optimized = [];
		$output = [];

		/**
		 *
		 *	Minimize Beta by constraining Expected Return.
		 *
		 */
		$result["request"] = [
			"expected_return" => $result["portfolio"]["statistics"]["expected_return"],
			"beta" => null
		];

		exec("{$python} ../storage/scripts/portfolio.py '" . json_encode($result) . "'", $output);
		$output = json_decode($output[ count($output) - 1 ], true);
		$output["request"] = $result["request"];
		array_push($optimized, $output);

		/**
		 *
		 *	Minimize Expected Return by constraining Beta.
		 *
		 */
		 $result["request"] = [
			"expected_return" => null,
			"beta" => $result["portfolio"]["statistics"]["beta"]
		];

	}

	else if($_SERVER["REQUEST_METHOD"] == "POST")
	{

		if(!array_key_exists("type", $_GET))
		{
			redirect("./optimize.php?id={$_GET['id']}");
		}
		else
		{
			if(empty($_POST["expected_return"]) && empty($_POST["beta"]))
			{
				redirect("./optimize.php?id={$_GET['id']}");
			}
		}

		$optimized = [];
		$output = [];

		if($_GET["type"] == "custom_constraint")
		{
			$er = floatval(array_key_exists("expected_return", $_POST) ? $_POST["expected_return"] : null);
			$beta = floatval(array_key_exists("beta", $_POST) ? $_POST["beta"] : null);

			$result["request"] = [
				"expected_return" => ($er == 0 || $er == null ? null : $er),
				"beta" => ($beta == 0 || $beta == null ? null : $beta)
			];
		}
		else
		{
			redirect("./optimize.php?id={$_GET['id']}");
		}		

	}

	$output = [];

	exec("{$python} ../storage/scripts/portfolio.py '" . json_encode($result) . "'", $output);
	$output = json_decode($output[ count($output) - 1 ], true);
	$output["request"] = $result["request"];
	array_push($optimized, $output);

	$status = array_map(function($element) {
		return abs($element["status"]);
	}, $optimized);

	$packet = [
		"optimized" => $optimized,
		"portfolio" => $result["portfolio"],
		"tickers" => $result["tickers"],
		"status" => array_sum($status) == 0 ? 0 : -1,
		"extended" => $result
	];

	if(array_key_exists("dump", $_GET) && $_GET["dump"] == "request")
	{
		header("Content-Type: application/json");
		echo json_encode($result);
	}
	else if(array_key_exists("dump", $_GET) && $_GET["dump"] == "packet")
	{
		header("Content-Type: application/json");
		echo json_encode($packet);
	}
	else
	{
		render("optimize.php", $packet);
	}
