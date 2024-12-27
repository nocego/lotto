<?php

$PageTitle="Bearbeite Serie";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the id from the URL
$id = $_GET['id'];
$lotto_id = $_GET['lotto_id'];
include_once('../config/db.php');
$sql = "SELECT name, date FROM Lotto where ID = ".$lotto_id . " and enabled = 1";
$result = $conn->query($sql);

if ($result->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

$sql = "SELECT name, lotto_id FROM Series where ID = ".$id;
$seriesResult = $conn->query($sql);
if ($seriesResult->num_rows > 0) {
    $row = $seriesResult->fetch_assoc();
    $name = $row["name"];
} else {
    header('Location: /lotto/lottos.php');
    exit();
}

include_once('../layout/header.php');
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Bearbeite <?=$name?></h1>
        </div>
    </div>
    <form action="/series/handle_update.php" method="post" class="">
        <input type="hidden" name="id" value="<?=$id?>">
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="Name" name="name" value='<?=$name?>' required>
        </div>
        <button type="submit" class="btn btn-success">Bearbeiten</button>
    </form>
</div>

<?php
include_once('../layout/footer.php');
?>