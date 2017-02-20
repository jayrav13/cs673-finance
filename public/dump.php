<?php

	// configuration
	require("../includes/config.php");

	$tables = CS50::query("SHOW TABLES");
	$output = [];

	for($i = 0; $i < count($tables); $i++)
	{
		$tables[$i] = array_values($tables[$i])[0];
		$result = CS50::query("SELECT * FROM {$tables[$i]}");
		$output[$tables[$i]] = $result;
	}

	$output = [
		"output" => $output,
		"title" => "Database Dump",
		"subtitle" => count($output) . " Tables"
	];
	render('dbdump.php', $output);
