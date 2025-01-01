<?php

$PageTitle="Spiele Serie";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the lotto_id from the URL
$id = $_GET['id'];
include_once('../config/db.php');

$sql = "SELECT * FROM Series where ID = ".$id;
$seriesResult = $conn->query($sql);
if ($seriesResult->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
} else {
    $seriesRow = $seriesResult->fetch_assoc();
    $lottoId = $seriesRow["lotto_id"];
}

$sql = "SELECT name, date FROM Lotto where ID = ".$lottoId." and enabled = 1";
$lottoResult = $conn->query($sql);

if ($lottoResult->num_rows < 1) {
    header('Location: /lotto/lottos.php');
    exit();
}

$sql = "SELECT * FROM Price where series_id = ".$seriesRow["ID"];
$priceResult = $conn->query($sql);

$sql = "SELECT * FROM Price where series_id = ".$seriesRow["ID"]." and winner_name IS NULL ORDER BY sequence ASC";
$pricesToWinResult = $conn->query($sql);

$nextPriceToWinRow = $pricesToWinResult->fetch_assoc();

if (isset($_SESSION['potential_winner_card_id']) && $_SESSION['potential_winner_card_id'] != "") {
    if ($nextPriceToWinRow == null) {
        $_SESSION['potential_winner_card_id'] = "";
    } else {
        $sql = "SELECT * FROM Card where ID = ".$_SESSION['potential_winner_card_id'];
        $winnerCardResult = $conn->query($sql);
        if ($winnerCardResult->num_rows > 0) {
            $winnerCardRow = $winnerCardResult->fetch_assoc();
            // convert row to json and echo
            $winnerCardJson = json_encode($winnerCardRow, JSON_UNESCAPED_UNICODE);

            $sql = "SELECT * FROM Number where series_id = ".$seriesRow["ID"]." ORDER BY ID DESC LIMIT 1";
            $numberResult = $conn->query($sql);
            $lastDrawnNumberRow = $numberResult->fetch_assoc();
            $lastDrawnNumber = $lastDrawnNumberRow['number'];
        }
    }
}

$sql = "SELECT * FROM Number where series_id = ".$seriesRow["ID"]." ORDER BY ID DESC";
$allDrawnNumbersResult = $conn->query($sql);

include_once('../layout/header.php');
?>

<style type="text/css">
    #drawnnumber {
        font-size: 100px;
    }
    #submit-btn {
        font-size: 100px;
    }
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

<div class="container">
    <?php
        if ($nextPriceToWinRow != null) {
    ?>
        <div class="row mb-3">
            <div class="col-12">
                <h1>Spiele <?=$seriesRow['name']?></h1>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <h2>Spielen</h2>
                <h3>Spiele um Preis "<?= $nextPriceToWinRow['sequence'] ?> - <?= $nextPriceToWinRow['name'] ?>"</h3>
                <form action="/series/handle_drawn_card.php" method="post" class="">
                    <input type="hidden" name="series_id" value="<?=$id?>">
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <input type="number" class="form-control" id="drawnnumber" placeholder="Gezogene Zahl" name="drawnnumber" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-success" id="submit-btn">Speichern</button>
                        </div>
                    </div>
                </form>
                <div class="mt-4">
                    <a class='btn btn-primary' href='/series/handle_lotto_saal.php?series_id=<?=$seriesRow["ID"]?>'>Lotto Saal</a>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
    <div class="row mt-4">
        <div class="col-12">
            <h2>Preise</h2>
            <table id="prices" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Reihenfolge</th>
                    <th>Preis</th>
                    <th>Sponsor</th>
                    <th>Sieger</th>
                    <th>Siegerzahl</th>
                    <th class="no-sort"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($priceResult->num_rows > 0) {
                    // Output data of each row
                    while($row = $priceResult->fetch_assoc()) {
                        $winnerString = "";
                        if ($row["winner_name"] != null) {
                            $winnerString .= $row["winner_name"];
                        }
                        if ($row["winner_birthyear"] != null) {
                            if ($winnerString != "") {
                                $winnerString .= ", ";
                            }
                            $winnerString .= $row["winner_birthyear"];
                        }
                        if ($row["winner_location"] != null) {
                            if ($winnerString != "") {
                                $winnerString .= ", ";
                            }
                            $winnerString .= $row["winner_location"];
                        }
                        if ($row["winner_seller"] != null) {
                            if ($winnerString != "") {
                                $winnerString .= "<br>";
                            }
                            $winnerString .= "Verkäufer: " . $row["winner_seller"];
                        }
                        if ($row["winner_card_number"] != null) {
                            if ($winnerString != "") {
                                $winnerString .= "<br>";
                            }
                            $winnerString .= "Kartennummer: " . $row["winner_card_number"];
                        }
                        if ($row["winner_number_1"] != null) {
                            if ($winnerString != "") {
                                $winnerString .= "<br>";
                            }
                            $winnerString .= "Zahl 1: " . $row["winner_number_1"];
                        }
                        if ($row["winner_number_2"] != null) {
                            if ($winnerString != "") {
                                $winnerString .= "<br>";
                            }
                            $winnerString .= "Zahl 2: " . $row["winner_number_2"];
                        }
                        echo "
                            <tr>
                                <td>" . htmlspecialchars($row["sequence"]) . "</td>
                                <td>" . htmlspecialchars($row["name"]) . "</td>
                                <td>" . htmlspecialchars($row["sponsor"]) . "</td>
                                <td>" . $winnerString . "</td>
                                <td>" . htmlspecialchars($row["winner_number"]) . "</td>
                                <td class='text-end'>
                                    <a class='btn btn-primary btn-sm' href='/price/update.php?id=".htmlspecialchars($row["ID"])."'><i class='fa fa-pen'></i></a>
                                </td>
                            </tr>";
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>Reihenfolge</th>
                    <th>Preis</th>
                    <th>Sponsor</th>
                    <th>Sieger</th>
                    <th>Siegerzahl</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <h2>Gezogene Zahlen</h2>
            <?php
                foreach ($allDrawnNumbersResult as $row) {
                    echo $row['number'] . "<br>";
                }
            ?>
        </div>
    </div>
</div>

<!-- modal -->
<div class="modal fade" id="potentialWinnerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Lotto Computer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Preis: <?= $nextPriceToWinRow['sequence'] ?> - <?= $nextPriceToWinRow['name'] ?><br>
                Gezogene Zahl: <?= $lastDrawnNumber ?><br><br>
                Gewinner: <br>
                <table border="1px solid black" style="table-layout: fixed; width: 100%;">
                    <tr>
                        <td>Kartennr.</td>
                        <td><?= $winnerCardRow['card_nr'] ?></td>
                    </tr>
                    <tr>
                        <td>Name/Vorname</td>
                        <td><?= $winnerCardRow['name'] ?> <?= $winnerCardRow['firstname'] ?></td>
                    </tr>
                    <tr>
                        <td>Jahrgang</td>
                        <td><?= $winnerCardRow['birthyear'] ?></td>
                    </tr>
                    <tr>
                        <td>Wohnort</td>
                        <td><?= $winnerCardRow['location'] ?></td>
                    </tr>
                    <tr>
                        <td>Verkäufer</td>
                        <td><?= $winnerCardRow['seller'] ?></td>
                    </tr>
                    <tr>
                        <td>Zahl 1</td>
                        <td><?= $winnerCardRow['number_1'] ?></td>
                    </tr>
                    <tr>
                        <td>Zahl 2</td>
                        <td><?= $winnerCardRow['number_2'] ?></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <a href="/series/save_lotto_computer.php?series_id=<?= $id ?>&card_id=<?= $winnerCardRow['ID'] ?>" class="btn btn-primary">Lotto Computer speichern</a>
                <a href="/series/handle_lotto_saal.php?series_id=<?= $id ?>" class="btn btn-primary">Lotto Saal</a>
                <a href="/series/ignore_lotto_computer.php?series_id=<?= $id ?>" class="btn btn-danger">Dieses Lotto Computer ignorieren</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#prices').DataTable({
            paging: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/de-DE.json',
            },
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false,
            }]
        });

        let winnerCardJson = <?php echo json_encode($winnerCardJson); ?>;
        if (winnerCardJson != null) {
            // show modal
            $('#potentialWinnerModal').modal('show');
        }
    });
</script>

<?php
include_once('../layout/footer.php');
?>
