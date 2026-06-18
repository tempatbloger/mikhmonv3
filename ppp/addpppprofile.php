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

// Ambil daftar profile untuk dropdown
if ($API->connect($iphost, $userhost, decrypt($passwdhost))) {
    $pools = $API->comm('/ip/pool/print');
} else {
    $pools = array();
}

// Proses simpan data
if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $localaddress = trim($_POST['localaddress']);
    $remoteaddress = trim($_POST['remoteaddress']);
    $ratelimit = trim($_POST['ratelimit']);
    $comment = trim($_POST['comment']);
    
    // Kirim command - hanya field yang diperlukan
    $API->comm("/ppp/profile/add", array(
        "name" => $name,
        "local-address" => $localaddress,
        "remote-address" => $remoteaddress,
        "rate-limit" => $ratelimit,
        "comment" => $comment,
    ));
    
    // Redirect ke halaman profile
    echo "<script>window.location='./?ppp=profiles&session=" . $session . "'</script>";
}
?>

<div class="row">
    <div class="col-12">
        <div class="card box-bordered">
            <div class="card-header">
                <h3><i class="fa fa-key"></i> Add PPP Profile <small id="loader" style="display: none;"><i><i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small></h3>
            </div>
            <div class="card-body">
                <form autocomplete="off" method="post" action="">
                    <div>
                        <a class='btn bg-warning' href='./?ppp=profiles&session=<?= $session ?>'> <i class='fa fa-close'></i> Close</a>
                        <button type="submit" onclick="loader()" class="btn bg-primary" name="save"><i class="fa fa-save"></i> Save</button>
                    </div>
                    <table class="table">
                        <tr>
                            <td class="align-middle">Name</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="name" value="" required="1" autofocus></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Local Address</td>
                            <td>
                                <input class="form-control" type="text" autocomplete="off" name="localaddress" value="" list="pool">
                                <datalist id="pool">
                                    <?php if (count($pools) > 0): ?>
                                        <?php foreach ($pools as $p): ?>
                                            <option><?= htmlspecialchars($p['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option>dhcp_pppoe</option>
                                    <?php endif; ?>
                                </datalist>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Remote Address</td>
                            <td>
                                <input class="form-control" type="text" autocomplete="off" name="remoteaddress" value="" list="pool2">
                                <datalist id="pool2">
                                    <?php if (count($pools) > 0): ?>
                                        <?php foreach ($pools as $p): ?>
                                            <option><?= htmlspecialchars($p['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option>dhcp_pppoe</option>
                                    <?php endif; ?>
                                </datalist>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Rate Limit [UP/Down]</td>
                            <td>
                                <input class="form-control" type="text" autocomplete="off" name="ratelimit" placeholder="Example : 512k/1M" value="">
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Comment</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="comment" value=""></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loader(){
    document.getElementById('loader').style.display = 'inline';
}
</script>

<?php include('../include/info.php'); ?>
</body>
</html>