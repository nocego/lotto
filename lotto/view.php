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
            <p>Sieger hier (inklusive export)</p>
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
                    ]
                }
            },
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false,
            }]
        });
        $('#cards').DataTable({
            paging: false,
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
