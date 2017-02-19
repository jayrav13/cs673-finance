<div class="row">
	<div class="col-md-6 col-md-offset-3">
	<?php foreach($portfolios as $portfolio) { ?>
		<h3><?php echo $portfolio["name"]; ?></h3>
	<?php } ?>
	</div>
</div>

<script type="text/javascript">

// On click, show modal.
$('#addPortfolio').on('shown.bs.modal', function () {
	$('#myInput').focus()
})
</script>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#addPortfolio">
	Launch demo modal
</button>

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