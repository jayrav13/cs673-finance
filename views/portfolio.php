<div class="row">
	<!-- Button trigger modal -->
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#buyShares">
		Buy Shares
	</button>
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sellShares">
		Sell Shares
	</button>
	<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#viewHistory">
		View History
	</button>
</div>
<br />
<div class="row">
	<div class="well well-sm col-md-4 col-md-offset-4">
		<h5 style="color: <?= $value["current"] > $value["original"] ? "green" : "red" ?> ">Value: $ <?= $value["current"] ?> (<?= $value["current"] > $value["original"] ? "+" : "-" ?> $ <?= $value["current"] - $value["original"] ?>)</h5>
		<h5>Balance: $ <?= $portfolio["cash"] ?></h5>
		<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCash"">+</button>
	</div>
</div>

<div class="container-fluid">
	<table class="table table-hover" style=" text-align: center ">
		<tr>
			<th class="text-center">ID</th>
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
				<td><?= $ticker["id"] ?> </td>
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
								<th>ID</th>
								<th>Value</th>
								<th>Total Cash</th>
								<th>Timestamp</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($transactions as $transaction) { ?>
							<tr>
								<td><?= $transaction["id"] ?></td>
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
							<th>ID</th>
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
							<td><?= $action["id"] ?></td>
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

