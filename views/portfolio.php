<?php
	$percentage_distro = [
		"USD" => $value["current"] > 0 ? $market_distro["USD"] / $value["current"] : 0,
		"INR" => $value["current"] > 0 ? $market_distro["INR"] / $value["current"] : 0,
	];
?>
<div class="row">
	<!-- Button trigger modal -->
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#buyShares">
		Buy Shares
	</button>
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sellShares">
		Sell Shares
	</button>
	<?php if($percentage_distro["USD"] > 0.8 || $percentage_distro["USD"] < 0.6) { ?>
	<!--<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#recommendedStocks">
		Recommended Stocks
	</button>-->
	<?php } ?>
	<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewHistory">
		View History
	</button>
	<a class="btn btn-info btn-sm" href="./download.php?portfolio_id=<?= $portfolio['id'] ?>">
		Dump to CSV
	</a>
	<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#expectedReturn">
		Portfolio Statistics
	</button>
	<hr />
	<a class="btn btn-success btn-sm" href="./optimize.php?id=<?= $portfolio['id'] ?>">
		Optimize
	</a>
	<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deletePortfolio">
		Delete Portfolio
	</button>
</div>
<br />
<div class="row">
	<div class="well well-sm col-md-4 col-md-offset-4">
		<h5 style="color: <?= $value["current"] > $value["original"] ? "green" : "red" ?> ">Value: $ <?= $value["current"] ?> (<?= $value["current"] > $value["original"] ? "+" : "-" ?> $ <?= $value["current"] - $value["original"] ?>)</h5>
		<h5 <?= $portfolio["cash"] < 0.1 * $value["current"] ? 'style="color: red;"' : "" ?>>Balance: $ <?= $portfolio["cash"] ?></h5>
		<span
			<?= $percentage_distro["USD"] > 0.8 || $percentage_distro["USD"] < 0.6 ?
				'style=" color: red; "' : "" ?>
			>Be sure to maintain a 70% USD to 30% INR ratio,<br />+ / - 10%, in your portfolio!<br /><br />
			<?= $value["current"] > 0 ? round(($market_distro["USD"] / $value["current"]) * 100, 3) : 0 ?>% USD - 
			<?= $value["current"] > 0 ? round(($market_distro["INR"] / $value["current"]) * 100, 3) : 0 ?>% INR<br />
		</span>
		<hr />
		<button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addCash">+</button>
	</div>
</div>

<div class="container-fluid">
	<table class="table table-hover" style=" text-align: center ">
		<tr>
			<th class="text-center">Symbol</th>
			<th class="text-center">Name</th>
			<th class="text-center">Exchange</th>
			<th class="text-center">Shares</th>
			<th class="text-center">Purchase Price</th>
			<th class="text-center">Current Price</th>
			<th class="text-center">Currency</th>
			<th class="text-center">Delta</th>
			<th class="text-center">Purchased On</th>
		</tr>
		<?php foreach($tickers as $ticker) { ?>
			<tr>
				<td><?= $ticker["symbol"] ?> </td>
				<td><?= $ticker["name"] ?> </td>
				<td><?= $exchanges[$ticker["exchange"]] ?> </td>
				<td><?= $ticker["shares"] ?> </td>
				<td><?= $ticker["price"] ?> </td>
				<td style="color: <?= $ticker['delta'] >= 0 ? 'green' : 'red' ?> ">
					<?= $ticker["current_price"] ?>
					<?php if ($ticker["currency"] != "USD" ) {
							echo " (" . currency_converter("INR", "USD", $ticker["current_price"], true) . " USD)" ;
						}
					?>
				</td>
				<td><?= $ticker["currency"] ?> </td>
				<td style="color: <?= $ticker['delta'] >= 0 ? 'green' : 'red' ?> "><?= $ticker["delta"] > 0 ? '+' : "-" ?><?= abs($ticker["delta"]) ?>
					<?php if ($ticker["currency"] != "USD" ) {
							echo " (" . currency_converter("INR", "USD", $ticker["delta"], true) . " USD)" ;
						}
					?>
					</td>
				<td><?= $ticker["created_at"] ?></td>
			</tr>
		<?php } ?>
	</table>
</div>

<script type="text/javascript">

// On click, show modal.
$('#buyShares').on('shown.bs.modal', function () {
	$('#myInput').focus()
})
</script>

<!-- Modal -->
<div class="modal fade" id="buyShares" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Buy Shares</h4>
			</div>
			<form method="post" action="portfolio.php?id=<?= $_GET["id"] ?>&action=shares&method=buy">
				<div class="modal-body">
					<label for="ticker">Ticker</label>
					<input type="text" class="form-control" id="ticker" name="ticker" placeholder="AAPL">
					<br />
					<label for="shares">No. of Shares</label>
					<input type="number" class="form-control" id="shares" name="shares" placeholder="20">
					<br />
					<label for="exchange">Exchange</label>
					<select class="form-control" name="exchange">
						<option value="">(search all)</option>
						<?php foreach($exchanges as $key => $exchange) { ?>
						<option value="<?= $key; ?>"><?= $exchange; ?></option>
						<?php } ?>
					</select>
					<br />
					<label for="exchange">Custom Price <snap style=" color: red; ">(OPTIONAL, default is live price.)</span></label>
					<input type="number" class="form-control" id="price" name="price" placeholder="135.72">
					<br />
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	// On click, show modal.
	$('#addCash').on('shown.bs.modal', function () {
		$('#myInput').focus()
	})
</script>

<!-- Modal -->
<div class="modal fade" id="addCash" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Add Cash</h4>
			</div>
			<form method="post" action="portfolio.php?id=<?= $_GET["id"] ?>&action=cash">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-3">
							<select class="form-control" name="operation">
								<option class="text-center" value="1">Add ($)</option>
								<option class="text-center" value="-1">Withdraw ($)</option>
							</select>
						</div>
						<div class="form-group col-md-7">
							<input class="form-control" name="cash" placeholder="300.00, -150.00" type="number"/>
						</div>
						<div class="form-group col-md-2">
							<button type="submit" class="btn btn-success btn-sm">Save</button>
						</div>
					</div>
					<hr />
					<h3>Transactional History</h3>
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Value</th>
								<th>Total Cash</th>
								<th>Timestamp</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($transactions as $transaction) { ?>
							<tr>
								<td><?= $transaction["value"] ?></td>
								<td><?= $transaction["cash"] ?></td>
								<td><?= $transaction["created_at"] ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	// On click, show modal.
	$('#viewHistory').on('shown.bs.modal', function () {
		$('#myInput').focus()
	})
</script>

<style type="text/css">
th {
	text-align: center;
}
</style>

<!-- Modal -->
<div class="modal fade" id="viewHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Stock History</h4>
			</div>
			<div class="modal-body">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Symbol</th>
							<th>Name</th>
							<th>Exchange</th>
							<th>Shares</th>
							<th>Price</th>
							<th>Currency</th>
							<th>Action</th>
							<th>Timestamp</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($actions as $action) { ?>
						<tr>
							<td><?= $action["symbol"] ?></td>
							<td><?= $action["name"] ?></td>
							<td><?= $exchanges[$action["exchange"]] ?></td>
							<td><?= $action["shares"] ?></td>
							<td><?= $action["price"] ?></td>
							<td><?= $action["currency"] ?></td>
							<td><?= $action["action"] ?></td>
							<td><?= $action["created_at"] ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<!--<button type="submit" class="btn btn-primary">Save changes</button>-->
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

// On click, show modal.
$('#sellShares').on('shown.bs.modal', function () {
	$('#myInput').focus()
})
</script>

<!-- Modal -->
<div class="modal fade" id="sellShares" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Sell Shares</h4>
			</div>
			<form method="post" action="portfolio.php?id=<?= $_GET["id"] ?>&action=shares&method=sell">
				<div class="modal-body">
					<label for="ticker">Ticker</label>

					<select class="form-control"  name="ticker_id">
						<?php foreach($tickers as $ticker) { ?>
							<option value="<?= $ticker['id'] ?>"><?= $ticker['symbol'] ?> - <?= $ticker["name"] ?> - <?= $exchanges[$ticker["exchange"]] ?> - <?= $ticker["shares"] ?> share(s)</option>
						<?php } ?>
					</select>

					<br />
					<label for="shares">No. of Shares</span></label>
					<input type="number" class="form-control" id="shares" name="shares" placeholder="20">
					<label for="price">Price</span></label>
					<input type="price" class="form-control" id="price" name="price" placeholder="143.90">
					<br />
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">

// On click, show modal.
$('#deletePortfolio').on('shown.bs.modal', function () {
	$('#myInput').focus()
})
</script>

<!-- Modal -->
<div class="modal fade" id="deletePortfolio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Delete Portfolio - Are You Sure?</h4>
			</div>
			<form method="post" action="portfolio.php?id=<?= $_GET["id"] ?>&action=delete">
				<div class="modal-body">

					All associated data (tickers, actions, transactions) will be deleted. This action CANNOT be undone.

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger">Delete Portfolio</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

// On click, show modal.
$('#expectedReturn').on('shown.bs.modal', function () {
	$('#myInput').focus()
})
</script>

<!-- Modal -->
<div class="modal fade" id="expectedReturn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Portfolio Statistics <small></small> </h4>
			</div>
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<table class="table table-hover">
								<tr>
									<td>Portfolio Expected Return</td>
									<td>$ <?= round($portfolio["statistics"]["expected_return"], 2) ?></td>
								</tr>
								<tr>
									<td>Portfolio Beta</td>
									<td><?= round($portfolio["statistics"]["beta"], 3) ?></td>
								</tr>
							</table>
						</div>
					</div>

					<table class="table table-hover">
					<thead>
						<tr>
							<th>Name</th>
							<th>Shares</th>
							<th>Purchase Price</th>
							<th>Live Price</th>
							<th>Expected Return</th>
							<th>Beta Value</th>
							<th>Currency</th>
							<th>Timestamp</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($tickers as $ticker) { ?>

						<tr>
							<td><?= $ticker["name"] ?></td>
							<td><?= $ticker["shares"] ?></td>
							<td>$ <?= $ticker["price"] ?></td>
							<td>$ <?= $ticker["current_price"] ?></td>
							<td>$ <?= round($ticker["historicals"]["expected_return"], 2) ?></td>
							<td><?= round($ticker["historicals"]["beta"], 3) ?></td>
							<td><?= $ticker["currency"] ?></td>
							<td><?= $ticker["created_at"] ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>

				<!--
				<div class="well well-sm">
				To calculate Projected Earnings (six weeks), we take the price on January 17th and determine the percent change between then and today's price, which is roughly six weeks separated.

				We use the below, simple formula:
				<hr />
				<pre>current_price + (current_price * percent_change(purchase_price, current_price))</pre>
				<hr />
				Based on this, we are able to determine how much the stock may be worth in six week if the previous six week's trend continues.

				This data is available by clicking <strong>Download to CSV</strong> on the Portfolio's main page.
				</div>
				-->

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
// On click, show modal.
$('#recommendedStocks').on('shown.bs.modal', function () {
	$('#myInput').focus()
})
</script>

<!-- Modal -->
<div class="modal fade" id="recommendedStocks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Recommended Stocks</h4>
			</div>
			<div class="modal-body">

			<div class="well well-sm">
			It looks like your portfolio doesn't have an even 70% / 30% balance between USR and INR stocks!<br /><br />
			Below is a list of assets you might find valuable - those that we believe will have a high Expected Return and those that we believe will be most stable; that is have a steady Beta value.<br /><br />Visit the <a href="./search.php" target="_BLANK">Search Tool</a> to learn more.
			</div>

			<div class="row">
				<h5 class="center">Top Projected Expected Return</h5>
				<div class="col-md-4 col-md-offset-4">
					<table class="table table-hover">
						<thead>
							<tr>
								<td>Ticker</td>
								<td>ER</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach(array_slice($suggestions["expected_return"], 0, 10) as $key => $value) { ?>
							<tr>
								<td><?php echo $key ?></td>
								<td><?php echo round($value * 100, 3) ?> %</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>


			<div class="row">
				<h5 class="center">Optimal Projected Expected Return</h5>
				<div class="col-md-4 col-md-offset-4">
					<table class="table table-hover">
						<thead>
							<tr>
								<td>Ticker</td>
								<td>Beta</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach(array_slice($suggestions["beta"], 0, 10) as $key => $value) { ?>
							<tr>
								<td><?php echo $key ?></td>
								<td><?php echo round($value, 3) ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger">Delete Portfolio</button>
			</div>
		</div>
	</div>
</div>