<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="page-header">
			<h1 class="header">Optimizr <small>Optimize Your Portfolio</small></h1>
		</div>
	</div>
</div>

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
									<td style="<?= $optimized[$i]["request"]["expected_return"] == null ? "background-color: green; font-weight: bold; color: white" : "" ?>">
										<?= $optimized[$i]["request"]["expected_return"] == null ? $optimized[$i]["fun"] * -1 : $optimized[$i]["request"]["expected_return"] ?>
									</td>
								</tr>
								<tr>
									<td>Beta Value</td>
									<td><?= $portfolio["statistics"]["beta"] ?></td>
									<td style="<?= $optimized[$i]["request"]["beta"] == null ? "background-color: green; font-weight: bold; color: white" : "" ?>">
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
			<div class="col-md-6 col-md-offset-3">
				<table class="table table-hover">
					<thead>
						<tr>
							<td class="center">Stock</td>
							<td class="center">Weight</td>
						</tr>
					</thead>

					<tbody>
						<?php for($j = 0; $j < count($tickers); $j++) { ?>
							<tr>
								<td><?php echo $tickers[$j]["symbol"]; ?></td>
								<td><?php echo $optimized[$i]["x"][$j]; ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>

	<?php } ?>

<?php } ?>



<div class="row">

</div>
