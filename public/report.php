<?php

	// configuration
	require("../includes/config.php");

	$headers = [

		"id",
		"symbol",
		"name",
		"exchange",
		"shares",
		"current_price",
		"weight",
		"currency",
		"initial_purchase_date",
		"expected_return",
		"beta"

	];

	$csv = [];

	// Dump headers w/empty file if the GET variable doesn't exist.
	if( ! array_key_exists("id", $_GET) )
	{
		array_push($csv, $headers);
		array_to_csv_download($csv);
		exit();
	}

	if($_SERVER["REQUEST_METHOD"] == "GET" || empty($_POST["data"]))
	{
		redirect("./");
	}

	$data = json_decode($_POST["data"], true);
	if($data == null)
	{
		redirect("./");
	}

	$user = CS50::query("SELECT * FROM users WHERE id = ?", $_SESSION["cs673_id"]);
	$portfolio = CS50::query("SELECT * FROM portfolios WHERE id = ? AND user_id = ?", $_GET["id"], $_SESSION["cs673_id"]);

	if($user == null || $portfolio == null)
	{
		array_push($csv, $headers);
		array_to_csv_download($csv);
		exit();
	}
	else
	{
		$user = $user[0];
		$portfolio = $portfolio[0];
	}

	// Static permalink to name conversions.
	$exchanges = [
		"nyse" => "New York Stock Exchange",
		"nasdaq" => "NASDAQ",
		"nse" => "National Stock Exchange of India Ltd."
	];

	$portfolio_intro = [
		"Portfolio \"{$portfolio['name']}\" for {$user['name']}",
		""
	];

	$statistics = [
		"Original Expected Return: {$data['portfolio']['statistics']['expected_return']}",
		"Original Beta: {$data['portfolio']['statistics']['beta']}",
		""
	];

	$csv = array_merge($csv, $portfolio_intro, $statistics);
	array_push($csv, $headers);

	for($i = 0; $i < count($data["extended"]["tickers"]); $i++)
	{
		$ticker = $data["extended"]["tickers"][$i];
		array_push($csv, [
			$ticker["id"],
			$ticker["symbol"],
			$ticker["name"],
			$exchanges[$ticker["exchange"]],
			$ticker["shares"],
			$ticker["current_price"],
			round($ticker["value"] / $data["extended"]["value"]["current"], 3),
			$ticker["currency"],
			$ticker["created_at"],
			"$ " . round($ticker["historicals"]["expected_return"], 2),
			round($ticker["historicals"]["beta"], 3)
		]);
	}

	$er = null;
	$beta = null;

	if($data["optimized"]["request"]["expected_return"] == null)
	{
		$er = abs($data["optimized"]["fun"]);
	}
	else
	{
		$er = $data["optimized"]["request"]["expected_return"];
	}

	if($data["optimized"]["request"]["beta"] == null)
	{
		$beta = abs($data["optimized"]["fun"]);
	}
	else
	{
		$beta = $data["optimized"]["request"]["beta"];
	}

	$portfolio_intro = [
		"",
		"OPTIMIZED Portfolio \"{$portfolio['name']}\" for {$user['name']}",
		"Strategy: " . ($data["optimized"]["request"]["beta"] == null && $data["optimized"]["request"]["expected_return"] == null ? "Custom Constraints for both Expected Return and Beta Value" : ($data["optimized"]["request"]["beta"] == null ? "Minimized Beta Value" : "Maximized Expected Return")),
		"",
		"Optimized Expected Return: " . ( $er ),
		"Optimized Beta Value: " . ( $beta ),
		""
	];

	$headers = [

		"id",
		"symbol",
		"name",
		"exchange",
		"shares",
		"current_price",
		"weight",
		"currency",
		"initial_purchase_date",
		"expected_return",
		"beta"

	];


	$csv = array_merge($csv, $portfolio_intro);
	array_push($csv, $headers);

	for($i = 0; $i < count($data["extended"]["tickers"]); $i++)
	{
		$ticker = $data["extended"]["tickers"][$i];
		array_push($csv, [
			$ticker["id"],
			$ticker["symbol"],
			$ticker["name"],
			$exchanges[$ticker["exchange"]],
			round(($data["extended"]["value"]["current"] * $data["optimized"]["x"][$i]) / ($ticker["value"] / $ticker["shares"])),
			$ticker["current_price"],
			round($data["optimized"]["x"][$i], 3),
			$ticker["currency"],
			$ticker["created_at"],
			"$ " . $ticker["historicals"]["expected_return"],
			round($ticker["historicals"]["beta"], 3)
		]);
	}

	header("Content-Disposition: attachment; filename=\"optimizer_report.csv\"");
	header("Content-Type: application/force-download");

	foreach($csv as $row)
	{
		if(is_string($row))
		{
			echo $row . "\n";
		}
		else
		{
			echo implode(",", $row) . "\n";
		}
	}
