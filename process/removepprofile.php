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

// Pastikan session ada
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
    exit;
}

include_once('../include/config.php');
include_once('../include/readcfg.php');
include_once('../lib/routeros_api.class.php');

$API = new RouterosAPI();
$API->debug = false;

// Ambil ID dan nama profile dari URL
$pid = $_GET['remove-pprofile'];
$pname = isset($_GET['pname']) ? $_GET['pname'] : '';

// Koneksi ke RouterOS
$API->connect($iphost, $userhost, decrypt($passwdhost));

// Jika ada nama profile, cek dan hapus scheduler yang terkait
if (!empty($pname)) {
    $getmonid = $API->comm("/system/scheduler/print", array(
        "?name" => "$pname",
    ));
    if (isset($getmonid[0]['.id'])) {
        $monid = $getmonid[0]['.id'];
        $API->comm("/system/scheduler/remove", array(
            ".id" => "$monid",
        ));
    }
}

// Hapus profile PPP
$API->comm("/ppp/profile/remove", array(
    ".id" => "$pid",
));

// Redirect ke halaman profile
echo "<script>window.location='./?ppp=profiles&session=" . $session . "'</script>";
?>