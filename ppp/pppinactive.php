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
    // Ambil semua user PPP
    $all_users = $API->comm('/ppp/secret/print');
    
    // Ambil user yang sedang aktif
    $active_users = $API->comm('/ppp/active/print');
    
    // Buat array nama user aktif
    $active_names = array();
    foreach ($active_users as $active) {
        $active_names[] = $active['name'];
    }
    
    // Filter user yang TIDAK aktif (inactive) dan tidak di-disable
    $inactive = array_filter($all_users, function($user) use ($active_names) {
        // User dianggap inactive jika:
        // 1. Tidak ada di daftar aktif
        // 2. Tidak dalam keadaan disabled
        return !in_array($user['name'], $active_names) && 
               (!isset($user['disabled']) || $user['disabled'] != 'true');
    });
    
    $total = count($inactive);
} else {
    $inactive = array();
    $total = 0;
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="fa fa-unlink"></i> PPP Inactive (<?= $total ?> items)
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
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Name</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Password</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Service</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Caller ID</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Profile</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Local Address</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Remote Address</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Last Logged Out</th>
                                <th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> <?= $_comment ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total > 0): ?>
                                <?php foreach ($inactive as $user): ?>
                                <tr>
                                    <td>
                                        <i class="fa fa-edit"></i>
                                        <a href="./?secret=<?= $user['.id'] ?>&session=<?= $session ?>">
                                            <?= htmlspecialchars($user['name']) ?>
                                        </a>
                                    </td>
                                    <td>****</td>
                                    <td><?= isset($user['service']) ? htmlspecialchars($user['service']) : '' ?></td>
                                    <td><?= isset($user['caller-id']) ? htmlspecialchars($user['caller-id']) : '' ?></td>
                                    <td><?= isset($user['profile']) ? htmlspecialchars($user['profile']) : '' ?></td>
                                    <td><?= isset($user['local-address']) ? htmlspecialchars($user['local-address']) : '' ?></td>
                                    <td><?= isset($user['remote-address']) ? htmlspecialchars($user['remote-address']) : '' ?></td>
                                    <td><?= isset($user['last-logged-out']) ? htmlspecialchars($user['last-logged-out']) : '' ?></td>
                                    <td><?= isset($user['comment']) ? htmlspecialchars($user['comment']) : '' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No inactive PPP users</td>
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