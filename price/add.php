<?php

$PageTitle="Neuer Preis";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the lotto_id from the URL
$seriesId = $_GET['series_id'];
include_once('../config/db.php');

$sql = "SELECT lotto_id FROM Series where ID = ".$seriesId;
$seriesResult = $conn->query($sql);

if ($seriesResult->num_rows > 0) {
    $lottoId = $seriesResult->fetch_assoc()['lotto_id'];
} else {
    header('Location: /lotto/lottos.php');
    exit();
}

$sql = "SELECT name, date FROM Lotto where ID = ".$lottoId." and enabled = 1";
$lottoResult = $conn->query($sql);

if ($lottoResult->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

$nextSequence = 1;
$sql = "SELECT sequence FROM Price where series_id = ".$seriesId." ORDER BY sequence DESC LIMIT 1";
$sequenceResult = $conn->query($sql);
if ($sequenceResult->num_rows > 0) {
    $nextSequence = $sequenceResult->fetch_assoc()['sequence'] + 1;
}

include_once('../layout/header.php');
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Neuer Preis</h1>
        </div>
    </div>
    <form action="/price/handle_add.php" method="post" class="">
        <input type="hidden" name="series_id" value="<?=$seriesId?>">
        <div class="mb-3 mt-3">
            <input type="number" class="form-control" id="sequence" placeholder="Reihenfolge" name="sequence" value="<?=$nextSequence?>">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="Preis" name="name" required>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="sponsor" placeholder="Sponsor" name="sponsor">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_name" placeholder="Sieger Name" name="winner_name">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_birthyear" placeholder="Sieger Geburtsjahr" name="winner_birthyear">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_location" placeholder="Sieger Wohnort" name="winner_location">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_company" placeholder="Sieger Firma" name="winner_company">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_seller" placeholder="Sieger Verkäufer" name="winner_seller">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_card_number" placeholder="Sieger Kartennummer" name="winner_card_number">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_number_1" placeholder="Sieger Zahl 1" name="winner_number_1">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_number_2" placeholder="Sieger Zahl 2" name="winner_number_2">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_number" placeholder="Siegerzahl" name="winner_number">
        </div>
        <button type="submit" class="btn btn-success">Erstellen</button>
    </form>
</div>

<?php
include_once('../layout/footer.php');
?>
