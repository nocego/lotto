<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//?>

<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// get the id from the URL
$seriesId = $_GET['series_id'];
include_once('../config/db.php');

$sql = "SELECT lotto_id, name FROM Series where ID = ".$seriesId;
$seriesResult = $conn->query($sql);
if ($seriesResult->num_rows > 0) {
    $row = $seriesResult->fetch_assoc();
    $lottoId = $row["lotto_id"];
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
$lottoRow = $lottoResult->fetch_assoc();

$sql = "SELECT * FROM Price where series_id = ".$seriesId;
$priceResult = $conn->query($sql);


include_once('../layout/header.php');
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h1><?=$lottoRow['name']?> - <?=$row['name']?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2>Preise</h2>
            <table id="prices" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Reihenfolge</th>
                        <th>Preis</th>
                        <th>Sponsor</th>
                        <th>Sieger</th>
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
                        if ($row["winner_company"] != null) {
                            if ($winnerString != "") {
                                $winnerString .= "<br>";
                            }
                            $winnerString .= "Firma: " . $row["winner_company"];
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
                                <td class='text-end'>
                                    <a class='btn btn-primary btn-sm' href='/price/update.php?id=".htmlspecialchars($row["ID"])."'><i class='fa fa-pen'></i></a>
                                    <a class='btn btn-danger btn-sm' href='#' onclick='confirmDelete(".htmlspecialchars($row["ID"]).")'><i class='fa fa-trash'></i></a>
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
                    <th></th>
                </tr>
                </tfoot>
            </table>
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
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: '<i class="fa fa-plus"></i> Neuer Preis',
                            className: 'btn btn-success', // Bootstrap 5 button classes
                            action: function (e, dt, node, config) {
                                window.location.href = '/price/add.php?series_id=<?=$seriesId?>';
                            }
                        }
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
        if (confirm('Bist du dir sicher, dass du diesen Preis löschen willst?')) {
            window.location.href = '/price/handle_delete.php?id=' + id;
        }
    }
</script>

<?php
include_once('../layout/footer.php');
?>
