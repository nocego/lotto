<?php

$PageTitle="Neues Lotto";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

include_once('../layout/header.php');
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Neues Lotto</h1>
        </div>
    </div>
    <form action="/lotto/handle_add.php" method="post" class="">
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="Name" name="name" required>
        </div>
        <div class="mb-3">
            <input type="date" class="form-control" id="date" placeholder="Datum" name="date" required>
        </div>
        <button type="submit" class="btn btn-success">Erstellen</button>
    </form>
</div>

<?php
    include_once('../layout/footer.php');
?>
