<?php
	// Src: http://php.net/manual/en/function.stats-covariance.php
	function covariance( $valuesA, $valuesB )
	{
		$countA = count($valuesA);
		$countB = count($valuesB);
		if ( $countA != $countB ) {
			trigger_error( 'Arrays with different sizes: countA='. $countA .', countB='. $countB, E_USER_WARNING );
			return false;
		}

		if ( $countA < 0 ) {
			trigger_error( 'Empty arrays', E_USER_WARNING );
			return false;
		}

		// Use library function if available
		if ( function_exists( 'stats_covariance' ) ) {
			return stats_covariance( $valuesA, $valuesB );
		}

		$meanA = array_sum( $valuesA ) / floatval( $countA );
		$meanB = array_sum( $valuesB ) / floatval( $countB );
		$add = 0.0;

		for ( $pos = 0; $pos < $countA; $pos++ ) {
			$valueA = $valuesA[ $pos ];
			if ( ! is_numeric( $valueA ) ) {
				trigger_error( 'Not numerical value in array A at position '. $pos .', value='. $valueA, E_USER_WARNING );
				return false;
			}

			$valueB = $valuesB[ $pos ];
			if ( ! is_numeric( $valueB ) ) {
				trigger_error( 'Not numerical value in array B at position '. $pos .', value='. $valueB, E_USER_WARNING );
				return false;
			}

			$difA = $valueA - $meanA;
			$difB = $valueB - $meanB;
			$add += ( $difA * $difB );
		} // for

		return $add / floatval( $countA );
	}

	// Src: http://php.net/manual/en/function.stats-standard-deviation.php
	if (!function_exists('stats_standard_deviation')) {
	    /**
	     * This user-land implementation follows the implementation quite strictly;
	     * it does not attempt to improve the code or algorithm in any way. It will
	     * raise a warning if you have fewer than 2 values in your array, just like
	     * the extension does (although as an E_USER_WARNING, not E_WARNING).
	     * 
	     * @param array $a 
	     * @param bool $sample [optional] Defaults to false
	     * @return float|bool The standard deviation or false on error.
	     */
	    function stats_standard_deviation(array $a, $sample = false) {
	        $n = count($a);
	        if ($n === 0) {
	            trigger_error("The array has zero elements", E_USER_WARNING);
	            return false;
	        }
	        if ($sample && $n === 1) {
	            trigger_error("The array has only 1 element", E_USER_WARNING);
	            return false;
	        }
	        $mean = array_sum($a) / $n;
	        $carry = 0.0;
	        foreach ($a as $val) {
	            $d = ((double) $val) - $mean;
	            $carry += $d * $d;
	        };
	        if ($sample) {
	           --$n;
	        }
	        return sqrt($carry / $n);
	    }
	}

	function get_expected_return_beta($portfolio, $tickers)
	{

		$portfolio["total_projection"] = 0;
		$portfolio["statistics"] = [
			"beta" => 0,
			"expected_return" => 0
		];

		// Prepare a value dict to store how much it's worth.
		$value = [
			"current" => 0,
			"original" => 0
		];

		$market_distro = [
			"USD" => 0,
			"INR" => 0,
		];

		// Conduct calculations per ticker.
		for($i = 0; $i < count($tickers); $i++)
		{

			// Get the live stock price of this ticker.
			$tickers[$i]["current_price"] = live_price($tickers[$i]["symbol"], $tickers[$i]["exchange"]);

			// Calculate the price change between the live price and the price at which this was purchased (in native currency).
			$tickers[$i]["delta"] = $tickers[$i]["current_price"] - $tickers[$i]["price"];

			// Calculate both the current and original portfolio values, this time converting foreign currencies to USD.
			if( $tickers[$i]["currency"] == "USD" )
			{
				$tickers[$i]["value"] = $tickers[$i]["current_price"] * $tickers[$i]["shares"];
				$value["current"] += $tickers[$i]["value"];
				$value["original"] += $tickers[$i]["price"] * $tickers[$i]["shares"];
			}
			else
			{
				$tickers[$i]["value"] = currency_converter( "INR", "USD", $tickers[$i]["current_price"], true ) * $tickers[$i]["shares"];
				$value["current"] += $tickers[$i]["value"];
				$value["original"] += currency_converter( "INR", "USD", $tickers[$i]["price"], true ) * $tickers[$i]["shares"];
			}

			$market_distro[$tickers[$i]["currency"]] += $tickers[$i]["value"];

			/**
			 *	Expected Return and Beta
			 *
			 *
			 */
			$historical_stock = historical_stock( $tickers[$i]["symbol"], $tickers[$i]["exchange"] );
			$historical_index = historical_index( $tickers[$i]["currency"] == "INR" ? "niftyfifty" : "sp500" );
			$tickers[$i]["historicals"] = [
				"stock" => $historical_stock,
				"index" => $historical_index,
				"beta" => beta_stock($historical_index, $historical_stock),
				"expected_return" => (stock_expected_return($tickers[$i]["symbol"], $tickers[$i]["exchange"])) * $tickers[$i]["value"],
			];

		}

		for($i = 0; $i < count($tickers); $i++)
		{
			$tickers[$i]["weight"] = $tickers[$i]["value"] / $value["current"];
			$portfolio["statistics"]["beta"] += $tickers[$i]["weight"] * $tickers[$i]["historicals"]["beta"];
			$portfolio["statistics"]["expected_return"] += $tickers[$i]["historicals"]["expected_return"] * $tickers[$i]["weight"];
		}

		return [
			"portfolio" => $portfolio,
			"tickers" => $tickers,
			"value" => $value,
			"market_distro" => $market_distro,
		];
	}

