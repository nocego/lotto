<?php

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

$date = new DateTime($date);
$dateFormatted = $date->format('d.m.Y');

$PageTitle=$name." - ".$dateFormatted;

include_once('../layout/header.php');
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1><?=$name?> - <?=$dateFormatted?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <p>Serien hier</p>
            <small>Serien und Preise erfassen?</small>
            <small>Dann könnte man die Serie auswählen, Zahl eingeben, lotto saal klicken oder den sieger übernehmen.</small>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <p>Spieler hier</p>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <p>Sieger hier (inklusive export)</p>
        </div>
    </div>
</div>

<?php
include_once('../layout/footer.php');
?>
