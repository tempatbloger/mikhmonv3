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

// Ambil daftar user PPP dan profile
if ($API->connect($iphost, $userhost, decrypt($passwdhost))) {
    $secret = $API->comm('/ppp/secret/print');
    $total = count($secret);
    $profiles = $API->comm('/ppp/profile/print');
} else {
    $secret = array();
    $total = 0;
    $profiles = array();
}

// Proses simpan data
if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $service = $_POST['service'];
    $profile = $_POST['profile'];
    $comment = trim($_POST['comment']);
    
    // Kirim command
    $API->comm("/ppp/secret/add", array(
        "name" => $name,
        "password" => $password,
        "service" => $service,
        "profile" => $profile,
        "comment" => $comment,
    ));
    
    // Redirect ke halaman user PPP
    echo "<script>window.location='./?ppp=secrets&session=" . $session . "'</script>";
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="fa fa-key"></i> <?= $_ppp_secrets ?> (<?= $total ?> items)
                    &nbsp;&nbsp; | &nbsp;&nbsp;
                    <a href="./?ppp=addsecret&session=<?= $session ?>">
                        <i class="fa fa-plus"></i> Add
                    </a>
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
                                <?php foreach ($secret as $user): ?>
                                <tr>
                                    <td style="text-align:center;">
                                        <i class="fa fa-minus-square text-danger pointer" 
                                           onclick="if(confirm('Are you sure to delete secret (<?= htmlspecialchars($user['name']) ?>)?')){window.location='./?remove-pppsecret=<?= $user['.id'] ?>&session=<?= $session ?>'}else{}" 
                                           title="Remove <?= htmlspecialchars($user['name']) ?>">
                                        </i>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php if (isset($user['disabled']) && $user['disabled'] == 'true'): ?>
                                            <a title="Enable secret <?= htmlspecialchars($user['name']) ?>" 
                                               href="./?enable-pppsecret=<?= $user['.id'] ?>&session=<?= $session ?>">
                                                <i class="fa fa-lock"></i>
                                            </a>
                                        <?php else: ?>
                                            <a title="Disable secret <?= htmlspecialchars($user['name']) ?>" 
                                               href="./?disable-pppsecret=<?= $user['.id'] ?>&session=<?= $session ?>">
                                                <i class="fa fa-unlock"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
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
                                    <td colspan="10" class="text-center">No PPP secrets found</td>
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