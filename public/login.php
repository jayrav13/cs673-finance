<?php

    // configuration
    require("../includes/config.php"); 

    // if user reached page via GET (as by clicking a link or via redirect)
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        // else render form
        render("login_form.php", ["title" => "Log In"]);
    }

    // else if user reached page via POST (as by submitting a form via POST)
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // validate submission
        if (empty($_POST["email"]))
        {
            render('login_form.php', ["title" => "Log In", 'errors' => ["You must provide your email."]]);
        }
        else if (empty($_POST["password"]))
        {
            render('login_form.php', ["title" => "Log In", 'errors' => ["You must provide your password."]]);
        }

        // query database for user
        $rows = CS50::query("SELECT * FROM users WHERE email = ?", $_POST["email"]);

        // if we found user, check password
        if (count($rows) == 1)
        {
            // first (and only) row
            $row = $rows[0];

            // compare hash of user's input against hash that's in database
            if (password_verify($_POST["password"], $row["password"]))
            {
                // remember that user's now logged in by storing user's ID in session
                $_SESSION["cs673_id"] = $row["id"];

                // redirect to portfolio
                redirect("/");
            }
        }

        // else apologize
        render('login_form.php', ["title" => "Log In", 'errors' => ["Invalid email and/or password."]]);
    }

?>
