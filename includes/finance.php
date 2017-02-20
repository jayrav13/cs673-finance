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

