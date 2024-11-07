<?php

$PageTitle="Neues Lotto";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the id from the URL
$id = $_GET['id'];
include_once('../config/db.php');
$sql = "SELECT name, date FROM Lotto where ID = ".$id;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["name"];
    $date = $row["date"];
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
    <form action="/lotto/handle_update.php" method="post" class="">
        <input type="hidden" name="id" value=<?=$id?>>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="Name" name="name" value=<?=$name?> required>
        </div>
        <div class="mb-3">
            <input type="date" class="form-control" id="date" placeholder="Datum" name="date" value=<?=$date?> required>
        </div>
        <button type="submit" class="btn btn-success">Bearbeiten</button>
    </form>
</div>

<?php
include_once('../layout/footer.php');
?>
