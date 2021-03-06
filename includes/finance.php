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

        $url = "http://www.google.com/finance/match?q={$q}";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);
        // $data = file_get_contents("http://www.google.com/finance/match?q={$q}");
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
            $price = (trim($crawler->filterXPath('//span[@class="pr"]')->text()));
            $price = floatval(str_replace(",", "", $price));
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
        $crawler = $client->request('GET', "https://www.google.com/finance?q={$exchange}:{$symbol}");

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
        $data = @file_get_contents($url);

        if($data == false)
        {
            return false;
        }

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
        $url = "http://www.google.com/finance/match?q={$q}";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);
        // $data = file_get_contents("http://www.google.com/finance/match?q={$q}");
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

    /**
     *  Converts either from USD to INR or reverse.
     */
    function currency_converter($from, $to, $value, $is_live=true)
    {

        // This is the default INR value for January 17, 2017.
        $INR = 66.86;
        if( ! $is_live )
        {
            // Get current INR price.
            $URL = "http://finance.yahoo.com/webservice/v1/symbols/allcurrencies/quote?format=json";
            $page = file_get_contents($URL);
            $data = json_decode($page, true)["list"]["resources"];
            $data = array_filter($data, function($element) {
                return $element["resource"]["fields"]["name"] == "USD/INR";
            });
            $INR = (float)(($data["21"]["resource"]["fields"]["price"]));
        }

        $result = null;
        if($from == "USD")
        {
            $result = $value * $INR;
        }
        else
        {
            $result = $value / $INR;
        }

        return floatval( number_format( $result, 2 ) );

    }

    /**
     *  Expected Return
     *
     *  Calculates a simple rate of increase or decrease in stock price to determine volatility.
     */
    function expected_return($symbol, $exchange)
    {
        // Get both Jan 17 and current prices.
        $init_price = init_price($symbol, $exchange);
        $live_price = live_price($symbol, $exchange);

        // Make sure all prices exist.
        if($init_price == false || $live_price == false)
        {
            return false;
        }

        // Calculate percent change.
        $change = ($live_price - $init_price) / $init_price;
        dump([
            $live_price,
            $init_price,
            $change
        ]);
    }

    function percent_change($init, $live)
    {
        return floatval(($live - $init) / $init);
    }

