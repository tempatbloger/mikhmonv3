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
    $profiles = $API->comm('/ppp/profile/print');
} else {
    $profiles = array();
}

// Proses simpan data - cara dari riadag2207 yang berhasil
if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $service = $_POST['service'];
    $profile = $_POST['profile'];
    $comment = trim($_POST['comment']);
    
    // Kirim command - hanya field yang diperlukan
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
    <div class="col-8">
        <div class="card box-bordered">
            <div class="card-header">
                <h3><i class="fa fa-key"></i> Add PPP Secret <small id="loader" style="display: none;"><i><i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small></h3>
            </div>
            <div class="card-body">
                <form autocomplete="off" method="post" action="">
                    <div>
                        <a class='btn bg-warning' href='./?ppp=secrets&session=<?= $session ?>'> <i class='fa fa-close'></i> Close</a>
                        <button type="submit" onclick="loader()" class="btn bg-primary" name="save"><i class="fa fa-save"></i> Save</button>
                    </div>
                    <table class="table">
                        <tr>
                            <td class="align-middle">Name</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="name" value="" required="1" autofocus></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Password</td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-11 col-box-10">
                                        <input class="group-item group-item-l" id="passUser" type="password" name="password" autocomplete="new-password" value="" required="1">
                                    </div>
                                    <div class="input-group-1 col-box-2">
                                        <div class="group-item group-item-r pd-2p5 text-center">
                                            <input title="Show/Hide Password" type="checkbox" onclick="PassUser()">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Services</td>
                            <td>
                                <select class="form-control" name="service" required="1">
                                    <option value="any">any</option>
                                    <option value="async">async</option>
                                    <option value="l2tp">l2tp</option>
                                    <option value="ovpn">ovpn</option>
                                    <option value="pppoe">pppoe</option>
                                    <option value="pptp">pptp</option>
                                    <option value="sstp">sstp</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Profile</td>
                            <td>
                                <select class="form-control" name="profile" required="1">
                                    <?php if (count($profiles) > 0): ?>
                                        <?php foreach ($profiles as $p): ?>
                                            <option value="<?= htmlspecialchars($p['name']) ?>"><?= htmlspecialchars($p['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="default">default</option>
                                    <?php endif; ?>
                                </select>
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
    <div class="col-4">
        <div class="card box-bordered">
            <div class="card-header">
                <h3><i class="fa fa-book"></i> Read Me </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 40%;">Rule</th>
                        <th>Penjelasan</th>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>Nama user PPP.</td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>Password user PPP.</td>
                    </tr>
                    <tr>
                        <td>Services</td>
                        <td>Jenis service (pppoe, pptp, dll).</td>
                    </tr>
                    <tr>
                        <td>Profile</td>
                        <td>Profile PPP yang akan digunakan.</td>
                    </tr>
                    <tr>
                        <td>Comment</td>
                        <td>Keterangan tambahan.</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function PassUser(){
    var x = document.getElementById('passUser');
    if (x.type === 'password') {
        x.type = 'text';
    } else {
        x.type = 'password';
    }
}
function loader(){
    document.getElementById('loader').style.display = 'inline';
}
</script>

<?php include('../include/info.php'); ?>
</body>
</html>