<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="page-header">
			<h1 class="header">Optimizr <small>Optimize Your Portfolio</small></h1>
		</div>
	</div>
</div>

<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#customConstraints">
Custom Optimization Constraints
</button>
<hr />
<?php if($status != 0) { ?>
	<div class="row">
		<div class="well well-sm center">
			The optimization process failed for one or more optimizations:<br />
			<?php
				for($i = 0; $i < count($optimized); $i++)
				{
					if($optimized[$i]["status"] != 0)
					{
						echo "Optimization " . ($i + 1) . ": " . $optimized[$i]["message"] . "<br />";
					}
				}
			?>
		</div>
	</div>
<?php } else { ?>

	<?php for($i = 0; $i < count($optimized); $i++) { ?>
		<div class="row">
			<div class="well well-sm">
				<b>Optimization:
				<?php
					if($optimized[$i]["request"]["expected_return"] == null && $optimized[$i]["request"]["beta"] != null) {
						echo "Maximized Expected Return";
					}
					else if($optimized[$i]["request"]["expected_return"] != null && $optimized[$i]["request"]["beta"] == null) {
						echo "Minimized Beta Value";
					}
					else {
						echo "Custom Constraints for both Expected Return and Beta Value";
					}
				?></b>
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<table class="table table-hover">
							<thead>
								<tr>
									<td></td>
									<td>Original</td>
									<td>Optimized</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Expected Return</td>
									<td><?= $portfolio["statistics"]["expected_return"] ?></td>
									<td style="<?= 
										( ($optimized[$i]["request"]["expected_return"] != null && $optimized[$i]["request"]["beta"] != null) || $optimized[$i]["request"]["expected_return"] == null) ? "background-color: green; font-weight: bold; color: white" : "" ?>">
										<?= $optimized[$i]["request"]["expected_return"] == null ? $optimized[$i]["fun"] * -1 : $optimized[$i]["request"]["expected_return"] ?>
									</td>
								</tr>
								<tr>
									<td>Beta Value</td>
									<td><?= $portfolio["statistics"]["beta"] ?></td>
									<td style="<?= ( ($optimized[$i]["request"]["expected_return"] != null && $optimized[$i]["request"]["beta"] != null) || $optimized[$i]["request"]["beta"] == null) ? "background-color: green; font-weight: bold; color: white" : "" ?>">
										<?= $optimized[$i]["request"]["beta"] == null ? $optimized[$i]["fun"] : $optimized[$i]["request"]["beta"] ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="table table-hover">
					<thead>
						<tr>
							<td class="center">Search</td>
							<td class="center">Stock</td>
							<td class="center">Weight</td>
							<td class="center">Price</td>
							<td class="center">Current Shares</td>
							<td class="center">Projected Shares</td>
							<td class="center">Delta Shares</td>
						</tr>
					</thead>

					<tbody>
						<?php for($j = 0; $j < count($tickers); $j++) { ?>
							<tr>
								<td>
									<form method="post" action="./search.php" target="_BLANK">
										<input id="ticker" name="ticker" type="text" hidden value="<?php echo $tickers[$j]["symbol"] ?>">
										<input id="exchange" name="exchange" type="text" hidden value="<?php echo $tickers[$j]["exchange"] ?>">
										<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
									</form>
								</td>
								<td><?php echo $tickers[$j]["symbol"]; ?></td>
								<td><?php echo $optimized[$i]["x"][$j]; ?></td>
								<td><?php echo $tickers[$j]["current_price"] ?></td>
								<td><?php echo $tickers[$j]["shares"] ?></td>
								<?php $new_shares = round(($optimized[$i]["x"][$j] * $extended["value"]["current"]) / $tickers[$j]["current_price"]); ?>
								<td><?php echo $new_shares ?></td>
								<td><?php echo $new_shares - $tickers[$j]["shares"] ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>

	<?php } ?>

<?php } ?>

<script type="text/javascript">
// On click, show modal.
$('#customConstraints').on('shown.bs.modal', function () {
	$('#myInput').focus()
})
</script>

<!-- Modal -->
<div class="modal fade" id="customConstraints" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Custom Optimization Constraints</h4>
			</div>
			<form method="post" action="./optimize.php?id=<?= $_GET["id"] ?>&type=custom_constraint">
				<div class="modal-body">
					<label for="expected_return">Expected Return</label>
					<input type="number" step="0.01" class="form-control" id="expected_return" name="expected_return" placeholder="1234.56">
					<br />
					<label for="beta">Beta Value</label>
					<input type="number" step="0.01" class="form-control" id="beta" name="beta" placeholder="0.923">
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

<div class="row">

</div>
