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
    $profile = $API->comm('/ppp/profile/print');
    $total = count($profile);
} else {
    $profile = array();
    $total = 0;
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="fa fa-pie-chart"></i> <?= $_ppp_profiles ?> (<?= $total ?> items)
                    &nbsp;&nbsp; | &nbsp;&nbsp;
                    <a href="./?ppp=add-profile&session=<?= $session ?>">Add</a>
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
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Local Address</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Remote Address</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Rate Limit</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Only One</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Parent Queue</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> <?= $_comment ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total > 0): ?>
                                <?php foreach ($profile as $p): ?>
                                <tr>
                                    <td style="text-align:center;">
                                        <i class="fa fa-minus-square text-danger pointer" 
                                           onclick="if(confirm('Are you sure to delete profile (<?= htmlspecialchars($p['name']) ?>)?')){window.location='./?remove-pprofile=<?= $p['.id'] ?>&pname=<?= urlencode($p['name']) ?>&session=<?= $session ?>'}else{}" 
                                           title="Remove <?= htmlspecialchars($p['name']) ?>">
                                        </i>
                                    </td>
                                    <td>
                                        <a href="./?ppp=edit-profile&idp=<?= $p['.id'] ?>&session=<?= $session ?>">
                                            <i class="fa fa-edit"></i> <?= htmlspecialchars($p['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= isset($p['local-address']) ? htmlspecialchars($p['local-address']) : '' ?></td>
                                    <td><?= isset($p['remote-address']) ? htmlspecialchars($p['remote-address']) : '' ?></td>
                                    <td><?= isset($p['rate-limit']) ? htmlspecialchars($p['rate-limit']) : '' ?></td>
                                    <td><?= isset($p['only-one']) ? htmlspecialchars($p['only-one']) : '' ?></td>
                                    <td><?= isset($p['parent-queue']) ? htmlspecialchars($p['parent-queue']) : '' ?></td>
                                    <td><?= isset($p['comment']) ? htmlspecialchars($p['comment']) : '' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No PPP profiles found</td>
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