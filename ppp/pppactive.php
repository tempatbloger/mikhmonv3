<?php
session_start();
if (!isset($_SESSION["mikhmon"])) {
    echo "<script>window.location='./admin.php?id=login'</script>";
    exit;
}

include_once('../include/menu.php');
include_once('../include/readcfg.php');
include_once('../include/lang.php');
include_once('../lang/'.$langid.'.php');

$API = new RouterosAPI();
$API->debug = false;

if ($API->connect($iphost, $userhost, decrypt($passwdhost))) {
    $active = $API->comm('/ppp/active/print');
    $total = count($active);
} else {
    $active = array();
    $total = 0;
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="fa fa-rocket"></i> <?= $_ppp_active ?> Connections (<?= $total ?> items)
                    &nbsp;&nbsp; | &nbsp;&nbsp;
                    <i onclick="location.reload();" class="fa fa-refresh pointer" title="Reload data"></i>
                </h3>
            </div>
            <div class="card-body">
                <div class="w-6">
                    <input id="filterTable" type="text" class="form-control" placeholder="Search..">
                </div>
                <div class="overflow box-bordered mr-t-10" style="max-height: 75vh">
                    <table id="dataTable" class="table table-bordered table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Name</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Service</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Caller ID</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Encoding</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Address</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Uptime</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> <?= $_comment ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total > 0): ?>
                                <?php foreach ($active as $user): ?>
                                <tr>
                                    <td style="text-align:center;">
                                        <i class="fa fa-minus-square text-danger pointer" 
                                           onclick="if(confirm('Are you sure to remove (<?= htmlspecialchars($user['name']) ?>)?')){window.location='./?remove-pactive=<?= $user['.id'] ?>&session=<?= $session ?>'}else{}" 
                                           title="Remove <?= htmlspecialchars($user['name']) ?>">
                                        </i>
                                    </td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= isset($user['service']) ? htmlspecialchars($user['service']) : '-' ?></td>
                                    <td><?= isset($user['caller-id']) ? htmlspecialchars($user['caller-id']) : '' ?></td>
                                    <td><?= isset($user['encoding']) ? htmlspecialchars($user['encoding']) : '' ?></td>
                                    <td><?= isset($user['address']) ? htmlspecialchars($user['address']) : (isset($user['remote-address']) ? htmlspecialchars($user['remote-address']) : '-') ?></td>
                                    <td><?= isset($user['uptime']) ? ' ' . formatDTM($user['uptime']) : '-' ?></td>
                                    <td><?= isset($user['comment']) ? htmlspecialchars($user['comment']) : '' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No active PPP users</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    makeAllSortable();
    $("#filterTable").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#dataTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>

<?php include('../include/info.php'); ?>
</body>
</html>