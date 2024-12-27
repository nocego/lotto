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
$sql = "SELECT ID, name FROM Series where lotto_id = ".$id;
$seriesResult = $conn->query($sql);

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
                    <th class="no-sort"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($seriesResult->num_rows > 0) {
                    // Output data of each row
                    while($row = $seriesResult->fetch_assoc()) {
                        $date = new DateTime($row["date"]);
                        echo "
                            <tr>
                                <td>" . htmlspecialchars($row["name"]) . "</td>
                                <td class='text-end'>
                                    <a class='btn btn-primary btn-sm' href='/price/view.php?series_id=".htmlspecialchars($row["ID"])."'><i class='fa fa-list'></i></a>
                                    <a class='btn btn-success btn-sm' href='/series/view.php?id=".htmlspecialchars($row["ID"])."'>TODO<i class='fa fa-play'></i></a>
                                    <a class='btn btn-primary btn-sm' href='/series/update.php?id=".htmlspecialchars($row["ID"])."&lotto_id=".htmlspecialchars($id)."'><i class='fa fa-pencil'></i></a>
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
                    <th></th>
                </tr>
                </tfoot>
            </table>
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
    });
    function confirmDelete(id) {
        if (confirm('Bist du dir sicher, dass du diese Serie löschen willst?')) {
            window.location.href = '/series/handle_delete.php?id=' + id;
        }
    }
</script>

<?php
include_once('../layout/footer.php');
?>
