<?php

	// configuration
	require("../includes/config.php"); 

	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
		// render portfolio
		$portfolios = CS50::query('SELECT * FROM portfolios WHERE user_id = ?', $_SESSION["cs673_id"]);

		for($i = 0; $i < count($portfolios); $i++)
		{
			$portfolios[$i]["tickers"] = [];
		}

		dump($portfolios);

		render("portfolio.php", ["title" => "Portfolio", "portfolios" => $portfolios]);
	}

	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		// Make sure the name parameter is available.
		if(empty($_POST["name"]))
		{
			render("portfolio.php", ["title" => "Portfolio", "errors" => ["A new portfolio could not be created - be sure to supply a name."]]);
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
				render("portfolio.php", ["title" => "Portfolio", "errors" => ["Something went wrong - please try again."]]);
			}
		}

	}

?>
