<?php

	// configuration
	require("../includes/config.php");

	var_dump(historical_data("AAPL"));
	var_dump(historical_data("BPCL", "nse"));