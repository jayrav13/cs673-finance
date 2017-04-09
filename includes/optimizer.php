<?php

    require_once("config.php");

    function historical_data($symbol, $exchange = null)
    {
    	$ticker = ticker($symbol, $exchange);
    	if(!$ticker)
    	{
    		return $ticker;
    	}

    	if($exchange == "nse")
    	{
    		$symbol = $symbol . ".NS";
    	}

    	// return file_get_contents("http://chart.finance.yahoo.com/table.csv?s=^NSEBANK&a=3&b=9&c=2014&d=3&e=9&f=2017&g=m&ignore=.csv");
    	return file_get_contents("http://chart.finance.yahoo.com/table.csv?s={$symbol}&a=3&b=9&c=2014&d=3&e=9&f=2017&g=d&ignore=.csv");

    }
