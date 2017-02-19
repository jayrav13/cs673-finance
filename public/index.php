<?php

	// configuration
	require("../includes/config.php"); 

	// render portfolio
	$portfolios = CS50::query('SELECT * FROM portfolios WHERE user_id = ?', $_SESSION["cs673_id"]);
	$title = "Portfolios";

	$output = [
		"portfolios" => $portfolios,
		"title" => $title
	];

	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
		render("index.php", $output);
	}

	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		// Make sure the name parameter is available.
		if(empty($_POST["name"]))
		{
			$output['errors'] = ["A new portfolio could not be created - be sure to supply a name."];
			render("index.php", $output);
		}
		else
		{
			$insert = CS50::query("INSERT INTO portfolios (name, user_id) VALUES (?, ?)", $_POST["name"], $_SESSION["cs673_id"]);
			if($insert == 1)
			{
				redirect('index.php');
			}
			else
			{
				$output['errors'] = ["Something went wrong - please try again."];
				render("index.php", $output);
			}
		}

	}

?>
