<div class="row">
	<!-- Button trigger modal -->
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#buyShares">
		Buy Shares
	</button>
</div>

<hr />

<div class="container-fluid">
	<table class="table table-hover container-fluid" style=" text-align: center ">
		<tr>
			<th class="text-center">ID</th>
			<th class="text-center">Symbol</th>
			<th class="text-center">Name</th>
			<th class="text-center">Exchange</th>
			<th class="text-center">Shares</th>
			<th class="text-center">Purchase Price</th>
			<th class="text-center">Current Price</th>
			<th class="text-center">Purchased On</th>
		</tr>
		<?php foreach($tickers as $ticker) { ?>
			<tr>
				<td><?= $ticker["id"] ?> </td>
				<td><?= $ticker["symbol"] ?> </td>
				<td><?= $ticker["name"] ?> </td>
				<td><?= $ticker["exchange"] ?> </td>
				<td><?= $ticker["shares"] ?> </td>
				<td><?= $ticker["price"] ?> </td>
				<td></td>
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
			<form method="post" action="portfolio.php?id=<?= $_GET["id"] ?>">
				<div class="modal-body">
					<input type="text" class="form-control" id="ticker" name="ticker" placeholder="AAPL">
					<br />
					<input type="number" class="form-control" id="shares" name="shares" placeholder="20">
					<br />
					<input type="number" class="form-control" id="price" name="price" placeholder="135.72">
					<br />
					<label for="select" class="text-left">Exchange</label>
					<select class="form-control" name="exchange">
						<option value="">(search all)</option>
						<?php foreach($exchanges as $key => $exchange) { ?>
						<option value="<?= $key; ?>"><?= $exchange; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>