<?php

    // configuration
    require("../includes/config.php"); 

    // if user reached page via GET (as by clicking a link or via redirect)
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        // else render form
        render("register_form.php", ["title" => "Register"]);
    }

    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // validate submission
        if (empty($_POST["name"]))
        {
            apologize("You must provide your name.");
        }
        else if (empty($_POST["email"]))
        {
            apologize("You must provide your username.");
        }
        else if (empty($_POST["password"]))
        {
            apologize("You must provide your username.");
        }
        else if (empty($_POST["confirm-password"]))
        {
            apologize("You must provide your password.");
        }

        // query database for user
        $rows = CS50::query("SELECT * FROM users WHERE email = ?", $_POST["email"]);

        // if we found user, check password
        if (count($rows) != 0)
        {

            apologize("Sorry - someone has already registered this email address. Please try again!");

        }

        else
        {
            $insert = CS50::query("INSERT INTO users (name, email, password) VALUES (?, ?, ?)", $_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT));
            $user = CS50::query("SELECT * FROM users WHERE email = ?", $_POST['email']);
            $_SESSION['id'] = $user[0]['id'];
            redirect('/');
        }

    }

?>
