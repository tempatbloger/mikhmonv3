<?php
session_start();
if (!isset($_SESSION["mikhmon"])) {
    echo "<script>window.location='./admin.php?id=login'</script>";
    exit;
}

include_once('../include/menu.php');
include_once('../include/readcfg.php');

$API = new RouterosAPI();
$API->debug = false;

if ($API->connect($iphost, $userhost, decrypt($passwdhost))) {
    // Ambil data PPP Secret
    $secret = $API->comm('/ppp/secret/print');
    $active = $API->comm('/ppp/active/print');
    $profile = $API->comm('/ppp/profile/print');
    
    $total_secret = count($secret);
    $total_active = count($active);
    $total_profile = count($profile);
} else {
    $total_secret = 0;
    $total_active = 0;
    $total_profile = 0;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">PPP Manager</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-users fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $total_secret ?></div>
                            <div>PPP Secret</div>
                        </div>
                    </div>
                </div>
                <a href="secret.php">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-user-md fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $total_active ?></div>
                            <div>PPP Active</div>
                        </div>
                    </div>
                </div>
                <a href="active.php">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-gear fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?= $total_profile ?></div>
                            <div>PPP Profile</div>
                        </div>
                    </div>
                </div>
                <a href="profile.php">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include('../include/info.php'); ?>
</body>
</html>
