<div class="row">
	<!-- Button trigger modal -->
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPortfolio">
		Create New Portfolio
	</button>
</div>

<hr />

<div class="row">

	<div class="col-md-8 col-md-offset-2">
	<?php if (isset($portfolios)): ?>
		<table class="table table-striped" style=" text-align: center; table-layout: fixed; ">
			<tr>
				<th class="text-center">Name</th>
				<th class="text-center">Date Created</th>
				<th class="text-center">View</th>
			</tr>
			<?php foreach($portfolios as $portfolio) { ?>
				<tr>
					<td><?= $portfolio["name"] ?></td>
					<td><?= $portfolio["created_at"] ?></td>
					<td><a href="./portfolio.php?id=<?= $portfolio["id"] ?>">Go</a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php endif ?>
</div>

<script type="text/javascript">

// On click, show modal.
$('#addPortfolio').on('shown.bs.modal', function () {
	$('#myInput').focus()
})
</script>

<!-- Modal -->
<div class="modal fade" id="addPortfolio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Create Portfolio</h4>
			</div>
			<form method="post" action="index.php">
				<div class="modal-body">
					<input type="text" class="form-control" id="name" name="name" placeholder="My First Portfolio">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>