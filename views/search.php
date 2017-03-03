<?php if(isset($info) && isset($init_price) && isset($live_price)): ?>
<div class="row">

<style type="text/css">
th {
	text-align: center;
}
</style>

<div class="well well-lg col-md-6 col-md-offset-3">

	<table class="table table-hover">
		<thead>
			<tr>
				<th>Name</th>
				<th>Symbol</th>
				<th>Exchange</th>
				<th>Jan 17 Price</th>
				<th>Live Price</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td><?= $info["name"] ?></td>
				<td><?= $info["ticker"] ?></td>
				<td><?= $info["exchange"] ?></td>
				<td><?= $init_price ?></td>
				<td><?= $live_price ?></td>
			</tr>
		</tbody>
	</table>
</div>

</div>
<?php endif ?>


<div class="row">
    <div class="col-md-6 col-md-offset-3 well well-lg">
        <form action="search.php" method="post">
            <fieldset>
            	<label for="exchange">Ticker</label>
                <div class="form-group">
                    <input autocomplete="off" autofocus class="form-control" name="ticker" placeholder="Name" type="text"/>
                </div>
                <label for="exchange">Exchange</label>
				<select class="form-control" name="exchange">
					<option value="">(select exchange)</option>
					<?php foreach($exchanges as $key => $exchange) { ?>
					<option value="<?= $key; ?>"><?= $exchange; ?></option>
					<?php } ?>
				</select>
				<hr />
                <div class="form-group">
                    <button class="btn btn-default" type="submit">
                        <span aria-hidden="true" class="glyphicon glyphicon-search"></span>
                        Search
                    </button>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div>
</div>
