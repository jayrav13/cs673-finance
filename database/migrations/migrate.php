<?php
    // CS50 library
    require("../../vendor/library50-php-5/CS50/CS50.php");
    CS50::init(__DIR__ . "/../../config.json");
    // Loop through files in database folder
    foreach(scandir(__DIR__) as $file) 
    {
        // Skip this folder and parent folder
        if($file == '.' || $file == '..') 
        {
            continue;
        }
        // Execute query with file contents if SQL file. On success, print.
        if(strpos($file, '.sql') !== false) 
        {
            $query = CS50::query(file_get_contents($file));
            if($query == 0) 
            {
                echo("Query executed: $file\n");
            }
            else 
            {
                echo("Review query: $file\n");
            }
        }
    }
?>
