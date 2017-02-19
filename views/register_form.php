<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <form action="register.php" method="post">
            <fieldset>
                <div class="form-group">
                    <input autocomplete="off" autofocus class="form-control" name="name" placeholder="Name" type="text"/>
                </div>
                <div class="form-group">
                    <input autocomplete="off" class="form-control" name="email" placeholder="Email" type="email"/>
                </div>
                <div class="form-group">
                    <input class="form-control" name="password" placeholder="Password" type="password"/>
                </div>
                <div class="form-group">
                    <input class="form-control" name="confirm-password" placeholder="Confirm Password" type="password"/>
                </div>
                <div class="form-group">
                    <button class="btn btn-default" type="submit">
                        <span aria-hidden="true" class="glyphicon glyphicon-log-in"></span>
                        Register
                    </button>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div>
    or <a href="login.php">login</a> to an existing account.
</div>
