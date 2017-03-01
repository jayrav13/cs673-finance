<div class="row">
	<!-- Button trigger modal -->
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#buyShares">
		Buy Shares
	</button>
</div>
<br />
<div class="row">
	<div class="well well-sm col-md-4 col-md-offset-4">
		<h5>Balance: $ <?= $portfolio["cash"] ?></h5>
		<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCash"">+</button>
	</div>
</div>

<div class="container-fluid">
	<table class="table table-hover container-fluid" style=" text-align: center ">
		<tr>
			<th class="text-center">ID</th>
			<th class="text-center">Symbol</th>
			<th class="text-center">Name</th>
			<th class="text-center">Exchange</th>
			<th class="text-center">Shares</th>
			<th class="text-center">Purchase Price</th>
			<th class="text-center">Currency</th>
			<th class="text-center">Current Price</th>
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
				<td><?= $ticker["currency"] ?> </td>
				<td style="color: <?= $ticker['delta'] >= 0 ? 'green' : 'red' ?> "><?= $ticker["current_price"] ?> (<?= $ticker["delta"] > 0 ? '+' : "-" ?><?= abs($ticker["delta"]) ?>) </td>
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
			<form method="post" action="portfolio.php?id=<?= $_GET["id"] ?>&action=shares">
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
					<label for="exchange">Custom Price</label>
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
						<div class="form-group col-md-9">
							<input class="form-control" name="cash" placeholder="300.00, -150.00" type="number"/>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

