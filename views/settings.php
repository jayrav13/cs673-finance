<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="well well-lg">
			<form action="settings.php" method="post">
			<fieldset>
				<div class="form-group">
					<input autocomplete="off" autofocus class="form-control" name="name" placeholder="Name" type="text" value="<?= $user['name'] ?>"/>
				</div>
				<div class="form-group">
					<input autocomplete="off" class="form-control" name="email" placeholder="Email" type="email" value="<?= $user['email'] ?>"/>
				</div>
				<div class="form-group">
					<input class="form-control" name="new-password" placeholder="New Password" type="password"/>
				</div>
				<div class="form-group">
					<input class="form-control" name="confirm-password" placeholder="Confirm Password" type="password"/>
				</div>
				<hr />
				<div class="alert alert-<?= $user["cash"] >= 0 ? "success" : "danger" ?>">
					BALANCE: $ <?= $user["cash"] ?>
				</div>
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
				<div class="form-group">
					<button class="btn btn-default" type="submit">
						<span aria-hidden="true" class="glyphicon glyphicon-log-in"></span>
						Update
					</button>
				</div>
			</fieldset>
		</form>
		</div>
		<?php if(isset($transactions)): ?>
		<div class="well well-lg">
			<h1 class="header">Transactions</h1>
			<table class="table table-hover">
				<thead>
					<th class="text-center">Owner</th>
					<th class="text-center">Transac. Value</th>
					<th class="text-center">Cash Total</th>
					<th class="text-center">Date</th>
				</thead>
				<tbody>
					<?php foreach($transactions as $transaction) { ?>
						<tr>
							<td> <?= $user["name"] ?> </td>
							<td> <?= $transaction["value"] ?> </td>
							<td> <?= $transaction["cash"] ?> </td>
							<td> <?= $transaction["created_at"] ?> </td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		</div>
		<?php endif ?>
	</div>
</div>