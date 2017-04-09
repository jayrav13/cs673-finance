<?php

    require_once("config.php");

    /**
     *  historical_stock
     *
     *  Return an array of all historical prices per month for the last 3 years.
     */
    function historical_stock($symbol, $exchange = null)
    {
    	// Check to make sure a ticker is valid.
    	$ticker = ticker($symbol, $exchange);
    	if(!$ticker)
    	{
    		return $ticker;
    	}

    	// Modify variables.
    	$symbol = strtolower($symbol);
    	$exchange = $exchange != null ? strtolower($exchange) : $exchange;

    	if($exchange == "nse")
    	{
    		$symbol = $symbol . ".NS";
    	}

    	// Get appropriate data.
    	$data = file_get_contents("http://chart.finance.yahoo.com/table.csv?s={$symbol}&a=3&b=9&c=2014&d=3&e=9&f=2017&g=m&ignore=.csv");

    	// Build into multidimensional array.
    	$data = array_map(function($element) {
    		return explode(",", $element);
    	}, explode("\n", $data));

    	// dump($data);

    	// Get all data points at the beginning of the month.
    	$filtered = array_map(function($element) {
    		return floatval($element[count($element) - 1]);
    	}, $data);

    	// Return all non-null values.
    	return array_values(array_filter($filtered, function($element) {
    		return $element != null;
    	}));

    }

    /**
     *  historical_index
     *
     *  Return the indicies historical data, per month, for 3 years.
     *  Possible inputs: ["niftyfifty", "sp500"]
     */
    function historical_index($index)
    {
    	if(!file_exists("../public/other/indices/{$index}.csv"))
    	{
    		return false;
    	}
    	else
    	{
    		$data = file_get_contents("../public/other/indices/{$index}.csv");

            $data = array_map(function($element) {
                return explode(",", $element);
            }, explode("\n", $data));

            $filtered = array_map(function($element) {
                return floatval($element[count($element) - 1]);
            }, $data);

            return array_values(array_filter($filtered, function($element) {
                return $element != 0 && $element != null;
            }));

    	}
    }

    /**
     *  array_changes
     *
     *  Given an array of floats / ints, return an array that's one element shorter
     *  that is the differences between each consecutive value.
     */
    function array_changes($arr)
    {
        $output = [];
        for($i = 1; $i < count($arr); $i++)
        {
            array_push($output, ($arr[$i] - $arr[$i - 1]) / $arr[$i - 1]);
        }
        return $output;
    }

    /** 
     *  beta_stock
     *
     *  Given arrays of an index and a stock, return the portfolio beta.
     */
    function beta_stock($index, $stock)
    {
        $covariance = covariance(array_changes($index), array_changes($stock));
        $stdev = stats_standard_deviation(array_changes($index), true);
        return ($covariance < 0 ? $covariance * -1 : $covariance) / ($stdev * $stdev);
    }
