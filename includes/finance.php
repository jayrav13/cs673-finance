<?php

    /**
     * finance.php
     *
     * Helper functions.
     */

    require_once("config.php");
    use Goutte\Client;

    /**
     *  Returns a stock by symbol via Google Finance.
     */
    function ticker($symbol, $exchange = null)
    {

        // Set query's default value, check for exchange to prepend.
        $q = $symbol;
        if($exchange != null)
        {
            $q = $exchange . ':' . $q;
        }

        // Retrieve JSON and decode.
        $data = file_get_contents("http://www.google.com/finance/match?q={$q}");
        $data = json_decode($data, true);

        // If nothing returns, fail.
        if(count($data["matches"]) == 0)
        {
            return false;
        }
        // If a list returns, pick the first one.
        else
        {
            $data = $data["matches"][0];
        }

        // Prepare scraper.
        $client = new Goutte\Client();
        $crawler = $client->request('GET', "https://www.google.com/finance?q={$data['e']}:{$data['t']}");

        // Attempt to extract price. If not available, fail.
        try
        {
            $price = floatval(trim($crawler->filterXPath('//span[@class="pr"]')->text()));
        }
        catch (Exception $e)
        {
            return false;
        }

        // Prepare an output array.
        $output = [];
        $output["price"] = $price;
        
        // Translate data to $output.
        $output["name"] = $data["n"];
        $output["ticker"] = $data["t"];
        $output["exchange"] = $data["e"];

        return $output;
    }

    function live_price($symbol, $exchange)
    {

        // Prepare scraper.
        $client = new Goutte\Client();
        $crawler = $client->request('GET', "https://www.google.com/finance?q={$data['e']}:{$data['t']}");

        // Attempt to extract price. If not available, fail.
        try
        {
            $price = floatval(trim($crawler->filterXPath('//span[@class="pr"]')->text()));
        }
        catch (Exception $e)
        {
            return false;
        }

        return $price;

    }

    /**
     *  Use this function to return adjusted close price of a stock on 1/17/2017.
     *  This function currently assumes that the symbol is valid.
     */
    function init_price($symbol, $exchange = null)
    {

        $query = $symbol;
        if($exchange == "nse")
        {
            $query = $query . ".ns";
        }

        // Query the provided symbol.
        $url = "http://chart.finance.yahoo.com/table.csv?s={$query}&a=0&b=17&c=2017&d=0&e=17&f=2017&g=d&ignore=.csv";
        $data = file_get_contents($url);

        // Break down by row and make sure data exists. Get first row.
        $data = explode("\n", $data);
        if(count($data) <= 1)
        {
            return false;
        }

        // Split up first date's stock data and grab adjusted close price.
        $data = explode(",", $data[1]);
        return floatval($data[6]) ;
    }

    /**
     *  Use this function to, given a symbol and exchange, get company info back.
     */
    function ticker_info($symbol, $exchange = null)
    {

        // Set query's default value, check for exchange to prepend.
        $q = $symbol;
        if($exchange != null)
        {
            $q = $exchange . ':' . $q;
        }

        // Retrieve JSON and decode.
        $data = file_get_contents("http://www.google.com/finance/match?q={$q}");
        $data = json_decode($data, true);

        // If nothing returns, fail.
        if(count($data["matches"]) == 0)
        {
            return false;
        }
        // If a list returns, pick the first one.
        else
        {
            // Translate data to $output.
            $data = $data["matches"][0];
            $data["name"] = $data["n"];
            $data["ticker"] = $data["t"];
            $data["exchange"] = $data["e"];
            return $data;
        }
    }
