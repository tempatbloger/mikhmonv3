<?php
/*
 *  Copyright (C) 2018 Laksamadi Guko.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
  exit;
}

// load session MikroTik
$session = isset($_GET['session']) ? $_GET['session'] : '';
$load = isset($_GET['load']) ? $_GET['load'] : '';

// lang
include('../include/lang.php');
include('../lang/'.$langid.'.php');

// load config
include('../include/config.php');
include('../include/readcfg.php');

// routeros api
include_once('../lib/routeros_api.class.php');
include_once('../lib/formatbytesbites.php');

$API = new RouterosAPI();
$API->debug = false;

// ============================================================
// LOAD SYS RESOURCE
// ============================================================
if ($load == "sysresource") {

    $API->connect($iphost, $userhost, decrypt($passwdhost));

    // get MikroTik system clock
    $getclock = $API->comm("/system/clock/print");
    $clock = $getclock[0];
    $timezone = $getclock[0]['time-zone-name'];
    date_default_timezone_set($timezone);

    // get system resource MikroTik
    $getresource = $API->comm("/system/resource/print");
    $resource = $getresource[0];

    // get routeboard info
    $getrouterboard = $API->comm("/system/routerboard/print");
    $routerboard = $getrouterboard[0];
    ?>
    
    <div id="r_1" class="row">
        <div class="col-4">
            <div class="box bmh-75 box-bordered">
                <div class="box-group">
                    <div class="box-group-icon"><i class="fa fa-calendar"></i></div>
                    <div class="box-group-area">
                        <span><?= $_system_date_time ?><br>
                            <?php 
                            echo ucfirst($clock['date']) . " " . $clock['time'] . "<br>
                            ".$_uptime." : " . formatDTM($resource['uptime']);
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="box bmh-75 box-bordered">
                <div class="box-group">
                    <div class="box-group-icon"><i class="fa fa-info-circle"></i></div>
                    <div class="box-group-area">
                        <span>
                            <?php
                            echo $_board_name." : " . $resource['board-name'] . "<br/>
                            ".$_model." : " . $routerboard['model'] . "<br/>
                            Router OS : " . $resource['version'];
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="box bmh-75 box-bordered">
                <div class="box-group">
                    <div class="box-group-icon"><i class="fa fa-server"></i></div>
                    <div class="box-group-area">
                        <span>
                            <?php
                            echo $_cpu_load." : " . $resource['cpu-load'] . "%<br/>
                            ".$_free_memory." : " . formatBytes($resource['free-memory'], 2) . "<br/>
                            ".$_free_hdd." : " . formatBytes($resource['free-hdd-space'], 2)
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php 
// ============================================================
// LOAD HOTSPOT
// ============================================================
} else if ($load == "hotspot") {

    $API->connect($iphost, $userhost, decrypt($passwdhost));

    // get & counting hotspot users
    $countallusers = $API->comm("/ip/hotspot/user/print", array("count-only" => ""));
    if ($countallusers < 2) {
        $uunit = "item";
    } elseif ($countallusers > 1) {
        $uunit = "items";
    }

    // get & counting hotspot active
    $counthotspotactive = $API->comm("/ip/hotspot/active/print", array("count-only" => ""));
    if ($counthotspotactive < 2) {
        $hunit = "item";
    } elseif ($counthotspotactive > 1) {
        $hunit = "items";
    }
    ?>
    
    <div id="r_2" class="card">
        <div class="card-header"><h3><i class="fa fa-wifi"></i> Hotspot</h3></div>
        <div class="card-body">
            <div class="row">
                <div class="col-3 col-box-6">
                    <div class="box bg-blue bmh-75">
                        <a href="./?hotspot=active&session=<?= $session; ?>">
                            <h1><?= $counthotspotactive; ?>
                                <span style="font-size: 15px;"><?= $hunit; ?></span>
                            </h1>
                            <div>
                                <i class="fa fa-laptop"></i> <?= $_hotspot_active ?>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-3 col-box-6">
                    <div class="box bg-green bmh-75">
                        <a href="./?hotspot=users&profile=all&session=<?= $session; ?>">
                            <h1><?= $countallusers; ?>
                                <span style="font-size: 15px;"><?= $uunit; ?></span>
                            </h1>
                            <div>
                                <i class="fa fa-users"></i> <?= $_hotspot_users ?>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-3 col-box-6">
                    <div class="box bg-yellow bmh-75">
                        <a href="./?hotspot-user=add&session=<?= $session; ?>">
                            <div>
                                <h1><i class="fa fa-user-plus"></i>
                                    <span style="font-size: 15px;"><?= $_add ?></span>
                                </h1>
                            </div>
                            <div>
                                <i class="fa fa-user-plus"></i> <?= $_hotspot_users ?>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-3 col-box-6">
                    <div class="box bg-red bmh-75">
                        <a href="./?hotspot-user=generate&session=<?= $session; ?>">
                            <div>
                                <h1><i class="fa fa-user-plus"></i>
                                    <span style="font-size: 15px;"><?= $_generate ?></span>
                                </h1>
                            </div>
                            <div>
                                <i class="fa fa-user-plus"></i> <?= $_hotspot_users ?>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php 
// ============================================================
// LOAD PPP
// ============================================================
} else if ($load == "ppp") {

    $API->connect($iphost, $userhost, decrypt($passwdhost));
    
    // PPP Active
    $ppp_active = $API->comm('/ppp/active/print');
    $count_active = count($ppp_active);
    $active_unit = $count_active > 1 ? 'items' : 'item';
    
    // PPP Secret
    $ppp_secret = $API->comm('/ppp/secret/print');
    $count_secret = count($ppp_secret);
    
    // PPP Inactive (tidak aktif, bukan disabled)
    $active_names = array();
    foreach ($ppp_active as $active) {
        $active_names[] = $active['name'];
    }
    
    $ppp_inactive = 0;
    foreach ($ppp_secret as $user) {
        if (!in_array($user['name'], $active_names) && 
            (!isset($user['disabled']) || $user['disabled'] != 'true')) {
            $ppp_inactive++;
        }
    }
    $inactive_unit = $ppp_inactive > 1 ? 'items' : 'item';
    ?>
    
    <div id="r_ppp" class="card">
        <div class="card-header">
            <h3><i class="fa fa-rocket"></i> PPP</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-3 col-box-6">
                    <div class="box bg-green bmh-75">
                        <a href="./?ppp=active&session=<?= $session ?>">
                            <h1>
                                <span id="dashboard_ppp_active"><?= $count_active ?></span>
                                <span style="font-size: 15px;" id="dashboard_ppp_active_unit"><?= $active_unit ?></span>
                            </h1>
                            <div>
                                <i class="fa fa-laptop"></i> PPP Active
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-3 col-box-6">
                    <div class="box bg-red bmh-75">
                        <a href="./?ppp=inactive&session=<?= $session ?>">
                            <div>
                                <h1>
                                    <span id="dashboard_ppp_inactive"><?= $ppp_inactive ?></span>
                                    <span style="font-size: 15px;" id="dashboard_ppp_inactive_unit"><?= $inactive_unit ?></span>
                                </h1>
                            </div>
                            <div>
                                <i class="fa fa-laptop"></i> PPP Inactive
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-3 col-box-6">
                    <div class="box bg-blue bmh-75">
                        <a href="./?ppp=secrets&session=<?= $session ?>">
                            <div>
                                <h1>
                                    <span id="dashboard_ppp_total"><?= $count_secret ?></span>
                                    <span style="font-size: 15px;">items</span>
                                </h1>
                            </div>
                            <div>
                                <i class="fa fa-users"></i> PPP Secret
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-3 col-box-6">
                    <div class="box bg-yellow bmh-75">
                        <a href="./?ppp=addsecret&session=<?= $session ?>">
                            <div>
                                <h1>
                                    <i class="fa fa-user-plus"></i>
                                    <span style="font-size: 15px;">Tambah</span>
                                </h1>
                            </div>
                            <div>
                                <i class="fa fa-user-plus"></i> PPP Profile
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php 
// ============================================================
// LOAD LOGS
// ============================================================
} else if ($load == "logs") {

    $API->connect($iphost, $userhost, decrypt($passwdhost));

    // move hotspot log to disk
    $getlogging = $API->comm("/system/logging/print", array("?prefix" => "->", ));
    $logging = $getlogging[0];
    if ($logging['prefix'] != "->") {
        $API->comm("/system/logging/add", array("action" => "disk", "prefix" => "->", "topics" => "hotspot,info,debug", ));
    }
    
    // get hotspot log
    $getlog = $API->comm("/log/print", array("?topics" => "hotspot,info,debug", ));
    $log = array_reverse($getlog);

    if ($livereport == "disable") {
        $logh = "457px";
    } else {
        $logh = "350px";
    }
    ?>
    
    <div id="r_3" class="row">
        <div class="card">
            <div class="card-header">
                <h3><a href="./?hotspot=log&session=<?= $session; ?>" title="Open Hotspot Log"><i class="fa fa-align-justify"></i> <?= $_hotspot_log ?></a></h3>
            </div>
            <div class="card-body">
                <div style="padding: 5px; height: <?= $logh; ?> ;" class="mr-t-10 overflow">
                    <table class="table table-sm table-bordered table-hover" style="font-size: 12px; td.padding:2px;">
                        <thead>
                            <tr>
                                <th><?= $_time ?></th>
                                <th><?= $_users ?> (IP)</th>
                                <th><?= $_messages ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $log_count = 0;
                            for ($i = 0; $i < 20 && $i < count($log); $i++) {
                                if (isset($log[$i])) {
                                    $message = $log[$i]['message'];
                                    $time = $log[$i]['time'];
                                    
                                    // Hanya tampilkan log yang dimulai dengan "->" (hotspot log)
                                    if (substr($message, 0, 2) == "->") {
                                        // Hapus "-> " dari awal message
                                        $clean_message = substr($message, 3);
                                        
                                        // Pisahkan berdasarkan ":"
                                        $parts = explode(":", $clean_message);
                                        
                                        echo "<tr>";
                                        echo "<td>" . $time . "</td>";
                                        
                                        // User (bagian pertama)
                                        if (isset($parts[0])) {
                                            echo "<td>" . trim($parts[0]) . "</td>";
                                        } else {
                                            echo "<td>-</td>";
                                        }
                                        
                                        // Pesan (gabungkan sisa bagian)
                                        $msg_parts = array_slice($parts, 1);
                                        $msg = implode(":", $msg_parts);
                                        $msg = str_replace("trying to", "", $msg);
                                        echo "<td>" . trim($msg) . "</td>";
                                        
                                        echo "</tr>";
                                        $log_count++;
                                    }
                                }
                            }
                            if ($log_count == 0) {
                                echo '<tr><td colspan="3" class="text-center">Tidak ada log hotspot</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php 
} // end if load

?>