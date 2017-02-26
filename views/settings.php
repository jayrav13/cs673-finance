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

				<div class="form-group">
					<button class="btn btn-default" type="submit">
						<span aria-hidden="true" class="glyphicon glyphicon-log-in"></span>
						Update
					</button>
				</div>
			</fieldset>
		</form>
		</div>
		
	</div>
</div>