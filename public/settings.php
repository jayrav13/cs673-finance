<?php

	// configuration
	require("../includes/config.php"); 

	// if user reached page via GET (as by clicking a link or via redirect)
	if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
		// else render form
		$transactions = CS50::query("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC", $_SESSION["cs673_id"]);
		render("settings.php", ["title" => "Settings", "transactions" => $transactions]);
	}
	else if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if(empty($_POST["name"]) || empty($_POST["email"]))
		{
			render("settings.php", ["errors" => ["Name and Email are both required."]]);
		}

		$update = CS50::query("UPDATE users SET name = ?, email = ? WHERE id = ?", $_POST["name"], $_POST["email"], $_SESSION["cs673_id"]);
		$user = CS50::query("SELECT * FROM users WHERE id = ?", $_SESSION["cs673_id"]);
		$user = $user[0];

		if( ! empty($_POST["new-password"]) && ! empty($_POST["confirm-password"]) && ( $_POST["new-password"] == $_POST["confirm-password"] ) )
		{
			$update = CS50::query("UPDATE users SET password = ? WHERE id = ?", password_hash($_POST['new-password'], PASSWORD_DEFAULT), $_SESSION["cs673_id"] );
		}

		redirect("./settings.php");

	}