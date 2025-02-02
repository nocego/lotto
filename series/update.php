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

$sql = "SELECT name, lotto_id, mode FROM Series where ID = ".$id;
$seriesResult = $conn->query($sql);
if ($seriesResult->num_rows > 0) {
    $row = $seriesResult->fetch_assoc();
    $name = $row["name"];
    $mode = $row["mode"];
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
        <div class="row">
            <input type="hidden" name="id" value="<?=$id?>">
            <div class="col-12 col-md-6">
                <div class="mb-3 mt-3">
                    <input type="text" class="form-control" id="name" placeholder="Name" name="name" value='<?=$name?>' required>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="mb-3 mt-3">
                    <select class="form-control" id="mode" name="mode" required>
                        <option value="1" <?php if($mode == 1){?>selected<?php }?>>Lottozahlen (gezogene Zahlen)</option>
                        <option value="2" <?php if($mode == 2){?>selected<?php }?>>Kartenzahlen (auf der verkauften Karte)</option>
                        <option value="3" <?php if($mode == 3){?>selected<?php }?>>Lottozahlen Saal (gezogene Zahlen, nur Lotto Saal)</option>
                        <option value="4" <?php if($mode == 4){?>selected<?php }?>>Kartenzahlen Saal (auf der verkauften Karte, nur Lotto Saal)</option>
                    </select>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">Bearbeiten</button>
            </div>
        </div>
    </form>
</div>

<?php
include_once('../layout/footer.php');
?>
