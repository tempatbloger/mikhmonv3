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

// Ambil parameter dari URL
$removesecr = isset($_GET['remove-pppsecret']) ? $_GET['remove-pppsecret'] : '';
$enablesecr = isset($_GET['enable-pppsecret']) ? $_GET['enable-pppsecret'] : '';
$disablesecr = isset($_GET['disable-pppsecret']) ? $_GET['disable-pppsecret'] : '';

// Koneksi ke RouterOS
$API->connect($iphost, $userhost, decrypt($passwdhost));

// Proses Remove
if (!empty($removesecr)) {
    $API->comm("/ppp/secret/remove", array(
        ".id" => "$removesecr",
    ));
}

// Proses Enable
if (!empty($enablesecr)) {
    $API->comm("/ppp/secret/enable", array(
        ".id" => "$enablesecr",
    ));
}

// Proses Disable
if (!empty($disablesecr)) {
    $API->comm("/ppp/secret/disable", array(
        ".id" => "$disablesecr",
    ));
}

// Redirect ke halaman user PPP
echo "<script>window.location='./?ppp=secrets&session=" . $session . "'</script>";
?>