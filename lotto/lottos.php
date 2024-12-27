<?php

$PageTitle = "Lottos";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == "") {
    header('Location: /login/login.php');
    exit();
}

// Include your database connection
include_once('../config/db.php');

// Get all lottos from the table Lotto
$sql = "SELECT ID, name, date FROM Lotto where enabled = 1";
$result = $conn->query($sql);

include_once('../layout/header.php');
?>

<div class="container">
    <div class="row">
        <div class="col">
            <h1>Lottos</h1>
            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Datum</th>
                    <th class="no-sort"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        $date = new DateTime($row["date"]);
                        echo "
                            <tr>
                                <td>" . htmlspecialchars($row["name"]) . "</td>
                                <td>" . htmlspecialchars($date->format('d.m.Y')) . "</td>
                                <td class='text-end'>
                                    <a class='btn btn-success btn-sm' href='/lotto/view.php?id=".htmlspecialchars($row["ID"])."'><i class='fa fa-eye'></i></a>
                                    <a class='btn btn-primary btn-sm' href='/lotto/update.php?id=".htmlspecialchars($row["ID"])."'><i class='fa fa-edit'></i></a>
                                    <!--<a class='btn btn-danger btn-sm' href='#' onclick='confirmDelete(".htmlspecialchars($row["ID"]).")'><i class='fa fa-trash'></i></a>-->
                                </td>
                            </tr>";
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Datum</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#example').DataTable({
            paging: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.1.8/i18n/de-DE.json',
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: '<i class="fa fa-plus"></i> Neues Lotto',
                            className: 'btn btn-success', // Bootstrap 5 button classes
                            action: function (e, dt, node, config) {
                                window.location.href = '/lotto/add.php';
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
    // function confirmDelete(id) {
    //     if (confirm('Are you sure you want to delete this record?')) {
    //         window.location.href = '/lotto/delete.php?id=' + id;
    //     }
    // }
</script>

<?php
include_once('../layout/footer.php');
?>
