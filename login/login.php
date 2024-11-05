<?php

$PageTitle="Login";

include_once('../layout/header.php');
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Login</h1>
        </div>
    </div>
    <form action="/login/handle_login.php" method="post" class="">
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="username" placeholder="Benutzername" name="username" required>
        </div>
        <div class="mb-3">
            <input type="password" class="form-control" id="pwd" placeholder="Passwort" name="pswd" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<?php
include_once('../layout/footer.php');
?>
