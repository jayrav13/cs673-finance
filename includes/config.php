<?php

    /**
     * config.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Configures app.
     */

    // display errors, warnings, and notices
    ini_set("display_errors", true);
    error_reporting(E_ALL);
    date_default_timezone_set("America/New_York");

    // requirements
    require("helpers.php");
    require("finance.php");
    require("optimizer.php");
    require("math/math.php");
    require("../vendor/autoload.php");

    // CS50 Library
    require("../vendor/library50-php-5/CS50/CS50.php");
    CS50::init(__DIR__ . "/../config.json");

    // enable sessions
    session_start();

    $url_prepend = "";
    if( array_key_exists('environment', CS50::config()) && array_key_exists('prepend', CS50::config()['environment']) )
    {
        $url_prepend = CS50::config()['environment']['prepend'];
    }

    // require authentication for all pages except /login.php, /logout.php, and /register.php
    if (!in_array($_SERVER["PHP_SELF"], [$url_prepend . "/login.php", $url_prepend . "/logout.php", $url_prepend . "/register.php", $url_prepend . "/dump.php"]))
    {
        if (empty($_SESSION["cs673_id"]))
        {
            redirect("login.php");
        }
    }

?>
