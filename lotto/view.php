<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the id from the URL
$id = $_GET['id'];
include_once('../config/db.php');
$sql = "SELECT name, date FROM Lotto where ID = ".$id." and enabled = 1";
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

// Get all lottos from the table Lotto
$sql = "SELECT ID, name, mode FROM Series where lotto_id = ".$id;
$seriesResult = $conn->query($sql);

$sql = "SELECT * FROM Card where lotto_id = ".$id;
$cardResult = $conn->query($sql);

$sql = "SELECT ID FROM Series where lotto_id = ".$id;
$seriesOfThisLottoResult = $conn->query($sql);
$seriesIds = [];
if ($seriesOfThisLottoResult->num_rows > 0) {
    while($row = $seriesOfThisLottoResult->fetch_assoc()) {
        $seriesIds[] = $row['ID'];
    }
}
$idsString = implode(',', $seriesIds);

$sql = "SELECT Series.name as `Seriesname`, Price.name as `Pricename`, Price.sponsor, Price.winner_name, Price.winner_birthyear, Price.winner_location, Price.winner_seller, Price.winner_card_number, Price.winner_number_1, Price.winner_number_2  FROM Price INNER JOIN Series on Series.ID = Price.series_id where series_id in (".$idsString.") and winner_name is not null ORDER BY series_id ASC, sequence ASC";
$winnersResult = $conn->query($sql);

$sql = "SELECT COUNT(*) as `count`, MAX(card_nr) as `max`, MIN(card_nr) as `min`, MAX(number_1) as `max1`, MIN(number_1) as `min1`, MAX(number_2) as `max2`, MIN(number_2) as `min2` FROM Card where lotto_id = ".$id;
$triviaResult = $conn->query($sql);
$trivia = $triviaResult->fetch_assoc();

$sql = "SELECT number, COUNT(*) as count FROM Number WHERE series_id IN (".$idsString.") GROUP BY number ORDER BY count DESC LIMIT 5";
$numbersResult = $conn->query($sql);

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
            <h2>Serien</h2>
            <table id="series" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Modus</th>
                    <th class="no-sort"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($seriesResult->num_rows > 0) {
                    // Output data of each row
                    while($row = $seriesResult->fetch_assoc()) {
                        $date = new DateTime($row["date"]);
                        if ($row["mode"] == 1) {
                            $mode = "Lottozahlen (gezogene Zahlen)";
                        } else {
                            $mode = "Kartenzahlen (auf der verkauften Karte)";
                        }
                        echo "
                            <tr>
                                <td>" . htmlspecialchars($row["name"]) . "</td>
                                <td>" . $mode . "</td>
                                <td class='text-end'>
                                    <a class='btn btn-primary btn-sm' href='/price/view.php?series_id=".htmlspecialchars($row["ID"])."'><i class='fa fa-list'></i></a>
                                    <a class='btn btn-success btn-sm' href='/series/play.php?id=".htmlspecialchars($row["ID"])."'><i class='fa fa-play'></i></a>
                                    <a class='btn btn-primary btn-sm' href='/series/update.php?id=".htmlspecialchars($row["ID"])."&lotto_id=".htmlspecialchars($id)."'><i class='fa fa-pen'></i></a>
                                    <a class='btn btn-danger btn-sm' href='#' onclick='confirmDelete(".htmlspecialchars($row["ID"]).")'><i class='fa fa-trash'></i></a>
                                </td>
                            </tr>";
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Modus</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <h2>Karten</h2>
            <table id="cards" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Name & Vorname</th>
                    <th>Jahrgang</th>
                    <th>Wohnort</th>
                    <th>Verkäufer</th>
                    <th>Kartennummer</th>
                    <th>Zahl 1</th>
                    <th>Zahl 2</th>
                    <th class="no-sort"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($cardResult->num_rows > 0) {
                    // Output data of each row
                    while($row = $cardResult->fetch_assoc()) {
                        echo "
                            <tr>
                                <td>" . htmlspecialchars($row["name"]) . " " . htmlspecialchars($row["firstname"]) . "</td>
                                <td>" . htmlspecialchars($row["birthyear"]) . "</td>
                                <td>" . htmlspecialchars($row["location"]) . "</td>
                                <td>" . htmlspecialchars($row["seller"]) . "</td>
                                <td>" . htmlspecialchars($row["card_nr"]) . "</td>
                                <td>" . htmlspecialchars($row["number_1"]) . "</td>
                                <td>" . htmlspecialchars($row["number_2"]) . "</td>
                                <td class='text-end'>
                                    <a class='btn btn-primary btn-sm' href='/card/update.php?id=".htmlspecialchars($row["ID"])."'><i class='fa fa-pen'></i></a>
                                    <a class='btn btn-danger btn-sm' href='#' onclick='confirmDeleteCard(".htmlspecialchars($row["ID"]).")'><i class='fa fa-trash'></i></a>
                                </td>
                            </tr>";
                    }
                }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Name & Vorname</th>
                        <th>Jahrgang</th>
                        <th>Wohnort</th>
                        <th>Verkäufer</th>
                        <th>Kartennummer</th>
                        <th>Zahl 1</th>
                        <th>Zahl 2</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <h2>Sieger</h2>
            <table id="winners" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th class="no-sort">Serie</th>
                    <th class="no-sort">Preis</th>
                    <th class="no-sort">Sponsor</th>
                    <th class="no-sort">Sieger</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($winnersResult->num_rows > 0) {
                    // Output data of each row
                    while($row = $winnersResult->fetch_assoc()) {

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
                                <td>" . htmlspecialchars($row["Seriesname"]) . " " . htmlspecialchars($row["firstname"]) . "</td>
                                <td>" . htmlspecialchars($row["Pricename"]) . "</td>
                                <td>" . htmlspecialchars($row["sponsor"]) . "</td>
                                <td>" . $winnerString . "</td>
                            </tr>";
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>Serie</th>
                    <th>Preis</th>
                    <th>Sponsor</th>
                    <th>Sieger</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>


    <div class="row mt-5">
        <div class="col-12">
            <h2>Trivia</h2>
            <div class="row">
                <div class="col-12 col-md-4 mt-4">
                    <h3>Anzahl Karten:</h3>
                    <?=$trivia['count']?>
                </div>
                <div class="col-12 col-md-4 mt-4">
                    <h3>Höchste Nummer (Zahl|Karte):</h3>
                    <?php
                        if ($trivia['max1'] > $trivia['max2']) {
                            $max = $trivia['max1'];
                        } else {
                            $max = $trivia['max2'];
                        }
                    ?>
                    <?= $max . " | " . $trivia['max'] ?>
                </div>
                <div class="col-12 col-md-4 mt-4">
                    <h3>Tiefste Nummer (Zahl|Karte):</h3>
                    <?php
                        if ($trivia['min1'] < $trivia['min2']) {
                            $min = $trivia['min1'];
                        } else {
                            $min = $trivia['min2'];
                        }
                    ?>
                    <?= $min . " | " . $trivia['min'] ?>
                </div>
                <div class="col-12 mt-4">
                    <h3>Am häufigsten gezogene Zahlen:</h3>
                    <?php
                    if ($numbersResult->num_rows > 0) {
                        // Output data of each row
                        while($row = $numbersResult->fetch_assoc()) {
                            echo $row['number'] . " (" . $row['count'] . "x)<br>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/lotto/import_series.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="lotto_id" value="<?=$id?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Serien importieren</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger">Achtung: Falls bereits Serien existieren, können diesen beim Import Preise hinzugefügt werden. Anschliessen müssen alle Serien und Preise kontrolliert werden.</p>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="file" name="file_to_import" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" id="submit-btn">Serien importieren</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#series').DataTable({
            paging: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/de-DE.json',
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: '<i class="fa fa-plus"></i> Neue Serie',
                            className: 'btn btn-success', // Bootstrap 5 button classes
                            action: function (e, dt, node, config) {
                                window.location.href = '/series/add.php?lotto_id=<?=$id?>';
                            }
                        },
                        {
                            text: '<i class="fa fa-file-export"></i> Exportieren',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                window.location.href = '/lotto/export_series.php?lotto_id=<?=$id?>';
                            }
                        },
                        {
                            text: '<i class="fa fa-file-export"></i> Importieren',
                            className: 'btn btn-danger',
                            action: function (e, dt, node, config) {
                                $('#importModal').modal('show');
                            }
                        },
                    ]
                }
            },
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false,
            }]
        });
        $('#cards').DataTable({
            paging: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/de-DE.json',
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: '<i class="fa fa-plus"></i> Neue Karte',
                            className: 'btn btn-success',
                            action: function (e, dt, node, config) {
                                window.location.href = '/card/add.php?lotto_id=<?=$id?>';
                            }
                        },
                    ]
                }
            },
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false,
            }]
        });
        $('#winners').DataTable({
            paging: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/de-DE.json',
            },
            layout: {
                topStart: {},
                topEnd: {
                    buttons: [
                        {
                            text: '<i class="fa fa-file-export"></i> Exportieren',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                window.location.href = '/lotto/export_winners.php?lotto_id=<?=$id?>';
                            }
                        },
                    ]
                }
            },
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false,
            }]
        });
    });

    function confirmDelete(id) {
        if (confirm('Bist du dir sicher, dass du diese Serie löschen willst?')) {
            window.location.href = '/series/handle_delete.php?id=' + id;
        }
    }

    function confirmDeleteCard(id) {
        if (confirm('Bist du dir sicher, dass du diese Karte löschen willst?')) {
            window.location.href = '/card/handle_delete.php?id=' + id;
        }
    }
</script>

<?php
include_once('../layout/footer.php');
?>
