<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <form action="login.php" method="post">
            <fieldset>
                <div class="form-group">
                    <input autocomplete="off" autofocus class="form-control" name="email" placeholder="Email" type="email"/>
                </div>
                <div class="form-group">
                    <input class="form-control" name="password" placeholder="Password" type="password"/>
                </div>
                <div class="form-group">
                    <button class="btn btn-default" type="submit">
                        <span aria-hidden="true" class="glyphicon glyphicon-log-in"></span>
                        Log In
                    </button>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<div class="row">
    or <a href="register.php">register</a> for an account
</div>

<hr />

<?php if(CS50::config()['environment']['env'] == "local"): ?>
    <div class="row">
        <div class="alert alert-info col-md-6 col-md-offset-3 text-left">
            Welcome to <strong>LOCAL</strong> mode. Since this Web Application is still in development, we may be rebuilding our database from time to time. Please refer to the <a href="./dump.php">Database</a> tab on the top right to ensure your account is valid if you're having a hard time logging in.
        </div>
<?php endif ?>