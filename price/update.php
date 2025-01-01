<?php

$PageTitle="Bearbeite Preis";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the id from the URL
$id = $_GET['id'];
include_once('../config/db.php');

$sql = "SELECT * FROM Price where ID = ".$id;
$priceResult = $conn->query($sql);
if ($priceResult->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

$priceRow = $priceResult->fetch_assoc();

$sql = "SELECT * FROM Series where ID = ".$priceRow['series_id'];
$seriesResult = $conn->query($sql);
if ($seriesResult->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

$seriesRow = $seriesResult->fetch_assoc();
$lotto_id = $seriesRow['lotto_id'];

$sql = "SELECT name, date FROM Lotto where ID = ".$lotto_id . " and enabled = 1";
$result = $conn->query($sql);

if ($result->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}
$lottoRow = $result->fetch_assoc();

include_once('../layout/header.php');
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Bearbeite Preis in <?=$lottoRow['name']?> - <?=$seriesRow['name']?></h1>
        </div>
    </div>
    <form action="/price/handle_update.php" method="post" class="">
        <input type="hidden" name="id" value="<?=$id?>">
        <div class="mb-3 mt-3">
            <input type="number" class="form-control" id="sequence" placeholder="Reihenfolge" name="sequence" value="<?=$priceRow['sequence']?>">
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="name" placeholder="Preis" name="name" value='<?=$priceRow['name']?>' required>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="sponsor" placeholder="Sponsor" name="sponsor" value='<?=$priceRow['sponsor']?>' required>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_name" placeholder="Sieger Name" name="winner_name" value='<?=$priceRow['winner_name']?>'>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_birthyear" placeholder="Sieger Geburtsjahr" name="winner_birthyear" value='<?=$priceRow['winner_birthyear']?>'>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_location" placeholder="Sieger Wohnort" name="winner_location" value='<?=$priceRow['winner_location']?>'>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_seller" placeholder="Sieger Verkäufer" name="winner_seller" value='<?=$priceRow['winner_seller']?>'>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_card_number" placeholder="Sieger Kartennummer" name="winner_card_number" value='<?=$priceRow['winner_card_number']?>'>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_number_1" placeholder="Sieger Zahl 1" name="winner_number_1" value='<?=$priceRow['winner_number_1']?>'>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_number_2" placeholder="Sieger Zahl 2" name="winner_number_2" value='<?=$priceRow['winner_number_2']?>'>
        </div>
        <div class="mb-3 mt-3">
            <input type="text" class="form-control" id="winner_number" placeholder="Siegerzahl" name="winner_number" value='<?=$priceRow['winner_number']?>'>
        </div>
        <button type="submit" class="btn btn-success">Bearbeiten</button>
    </form>
</div>

<?php
include_once('../layout/footer.php');
?>
