<?php

$PageTitle="Neue Serie";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the lotto_id from the URL
$id = $_GET['lotto_id'];
include_once('../config/db.php');
$sql = "SELECT name, date FROM Lotto where ID = ".$id." and enabled = 1";
$lottoResult = $conn->query($sql);

if ($lottoResult->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

include_once('../layout/header.php');
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Neue Serie</h1>
        </div>
    </div>
    <form action="/series/handle_add.php" method="post" class="">
        <input type="hidden" name="lotto_id" value="<?=$id?>">
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="Name" name="name" required>
        </div>
        <button type="submit" class="btn btn-success">Erstellen</button>
    </form>
</div>

<?php
include_once('../layout/footer.php');
?>
